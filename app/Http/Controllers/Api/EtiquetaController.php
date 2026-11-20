<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Etiqueta;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EtiquetaController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $etiquetas = $request->user()->etiquetas()
            ->withCount('tarefas')
            ->orderBy('nome')
            ->get(['id', 'nome']);

        return response()->json(['data' => $etiquetas]);
    }

    public function destroy(Request $request, Etiqueta $etiqueta): Response
    {
        abort_unless($etiqueta->user_id === $request->user()->id, Response::HTTP_NOT_FOUND, 'Etiqueta não encontrada.');
        $etiqueta->delete();

        return response()->noContent();
    }
}
