<?php

namespace App\Enums;

enum Prioridade: string
{
    case Baixa = 'baixa';
    case Media = 'media';
    case Alta = 'alta';

    /** Peso usado para ordenar por prioridade (alta primeiro). */
    public function peso(): int
    {
        return match ($this) {
            self::Alta => 3,
            self::Media => 2,
            self::Baixa => 1,
        };
    }
}
