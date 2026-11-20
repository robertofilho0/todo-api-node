<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TarefaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descricao' => $this->descricao,
            'status' => $this->status,
            'prioridade' => $this->prioridade,
            'data_limite' => $this->data_limite?->toDateString(),
            'atrasada' => $this->estaAtrasada(),
            'concluida_em' => $this->concluida_em,
            'etiquetas' => $this->whenLoaded('etiquetas', fn () => $this->etiquetas->pluck('nome')->sort()->values()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
