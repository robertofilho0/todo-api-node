<?php

namespace App\Models;

use App\Enums\Prioridade;
use App\Enums\StatusTarefa;
use Database\Factories\TarefaFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tarefa extends Model
{
    /** @use HasFactory<TarefaFactory> */
    use HasFactory;

    protected $fillable = ['titulo', 'descricao', 'status', 'prioridade', 'data_limite'];

    protected $attributes = [
        'status' => 'pendente',
        'prioridade' => 'media',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusTarefa::class,
            'prioridade' => Prioridade::class,
            'data_limite' => 'date',
            'concluida_em' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Tarefa $tarefa) {
            // Registra quando a tarefa foi concluída (e limpa se for reaberta)
            if ($tarefa->isDirty('status')) {
                $tarefa->concluida_em = $tarefa->status === StatusTarefa::Concluida ? now() : null;
            }
            $tarefa->peso_prioridade = $tarefa->prioridade->peso();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function etiquetas(): BelongsToMany
    {
        return $this->belongsToMany(Etiqueta::class);
    }

    public function estaAtrasada(): bool
    {
        return $this->status !== StatusTarefa::Concluida
            && $this->data_limite !== null
            && $this->data_limite->isBefore(today());
    }

    public function scopeAtrasadas(Builder $query): Builder
    {
        return $query->where('status', '!=', StatusTarefa::Concluida->value)
            ->whereDate('data_limite', '<', today());
    }

    /** Busca no título e na descrição. */
    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        return $query->when($termo, fn (Builder $q) => $q->where(
            fn (Builder $w) => $w->where('titulo', 'like', "%{$termo}%")->orWhere('descricao', 'like', "%{$termo}%")
        ));
    }
}
