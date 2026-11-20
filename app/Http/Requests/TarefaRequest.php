<?php

namespace App\Http\Requests;

use App\Enums\Prioridade;
use App\Enums\StatusTarefa;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TarefaRequest extends FormRequest
{
    public function rules(): array
    {
        $obrigatorio = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'titulo' => [$obrigatorio, 'string', 'min:1', 'max:120'],
            'descricao' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', Rule::enum(StatusTarefa::class)],
            'prioridade' => ['sometimes', Rule::enum(Prioridade::class)],
            'data_limite' => ['nullable', 'date_format:Y-m-d'],
            'etiquetas' => ['sometimes', 'array', 'max:10'],
            'etiquetas.*' => ['string', 'distinct', 'min:1', 'max:30'],
        ];
    }
}
