<?php

namespace App\Http\Controllers\Api;

use App\Enums\Prioridade;
use App\Enums\StatusTarefa;
use App\Http\Controllers\Controller;
use App\Http\Requests\TarefaRequest;
use App\Http\Resources\TarefaResource;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TarefaController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $filtros = $request->validate([
            'status' => ['nullable', Rule::enum(StatusTarefa::class)],
            'prioridade' => ['nullable', Rule::enum(Prioridade::class)],
            'etiqueta' => ['nullable', 'string', 'max:30'],
            'busca' => ['nullable', 'string', 'max:100'],
            'atrasadas' => ['nullable', 'boolean'],
            'ordenar' => ['nullable', Rule::in(['created_at', 'data_limite', 'prioridade', 'titulo'])],
            'direcao' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'between:1,100'],
        ]);

        $ordenar = ($filtros['ordenar'] ?? 'created_at') === 'prioridade' ? 'peso_prioridade' : ($filtros['ordenar'] ?? 'created_at');

        $tarefas = $request->user()->tarefas()
            ->with('etiquetas')
            ->when($filtros['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filtros['prioridade'] ?? null, fn ($q, $prioridade) => $q->where('prioridade', $prioridade))
            ->when($filtros['etiqueta'] ?? null, fn ($q, $nome) => $q->whereHas('etiquetas', fn ($e) => $e->where('nome', $nome)))
            ->when($request->boolean('atrasadas'), fn ($q) => $q->atrasadas())
            ->busca($filtros['busca'] ?? null)
            ->orderBy($ordenar, $filtros['direcao'] ?? 'desc')
            ->orderBy('id')
            ->paginate($filtros['per_page'] ?? 10)
            ->withQueryString();

        return TarefaResource::collection($tarefas);
    }

    public function store(TarefaRequest $request): JsonResponse
    {
        $tarefa = DB::transaction(function () use ($request) {
            $tarefa = $request->user()->tarefas()->create($request->safe()->except('etiquetas'));
            $this->syncEtiquetas($request->user(), $tarefa, $request->validated('etiquetas'));

            return $tarefa;
        });

        return (new TarefaResource($tarefa->load('etiquetas')))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(Tarefa $tarefa): TarefaResource
    {
        Gate::authorize('manage', $tarefa);

        return new TarefaResource($tarefa->load('etiquetas'));
    }

    public function update(TarefaRequest $request, Tarefa $tarefa): TarefaResource
    {
        Gate::authorize('manage', $tarefa);

        DB::transaction(function () use ($request, $tarefa) {
            $tarefa->update($request->safe()->except('etiquetas'));
            $this->syncEtiquetas($request->user(), $tarefa, $request->validated('etiquetas'));
        });

        return new TarefaResource($tarefa->load('etiquetas'));
    }

    public function destroy(Tarefa $tarefa): Response
    {
        Gate::authorize('manage', $tarefa);
        $tarefa->delete();

        return response()->noContent();
    }

    public function estatisticas(Request $request): JsonResponse
    {
        $tarefas = $request->user()->tarefas();
        $porStatus = (clone $tarefas)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        $resumo = collect(StatusTarefa::cases())
            ->mapWithKeys(fn (StatusTarefa $s) => [$s->value => (int) ($porStatus[$s->value] ?? 0)]);

        $concluidas = $resumo[StatusTarefa::Concluida->value];
        $total = $resumo->sum();

        return response()->json([
            'total' => $total,
            'por_status' => $resumo,
            'atrasadas' => (clone $tarefas)->atrasadas()->count(),
            'vencem_hoje' => (clone $tarefas)->where('status', '!=', StatusTarefa::Concluida->value)->whereDate('data_limite', today())->count(),
            'percentual_concluido' => $total ? round($concluidas / $total * 100, 1) : 0,
        ]);
    }

    /** Cria as etiquetas que ainda não existem (por usuário) e sincroniza com a tarefa. */
    private function syncEtiquetas(User $user, Tarefa $tarefa, ?array $nomes): void
    {
        if ($nomes === null) {
            return;
        }

        $ids = collect($nomes)
            ->map(fn (string $nome) => mb_strtolower(trim($nome)))
            ->unique()
            ->map(fn (string $nome) => $user->etiquetas()->firstOrCreate(['nome' => $nome])->id);

        $tarefa->etiquetas()->sync($ids);
    }
}
