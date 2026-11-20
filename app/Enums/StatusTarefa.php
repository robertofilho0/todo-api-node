<?php

namespace App\Enums;

enum StatusTarefa: string
{
    case Pendente = 'pendente';
    case EmAndamento = 'em_andamento';
    case Concluida = 'concluida';
}
