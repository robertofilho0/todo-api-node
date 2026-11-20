<?php

namespace Tests\Unit;

use App\Enums\Prioridade;
use PHPUnit\Framework\TestCase;

class PrioridadeTest extends TestCase
{
    public function test_peso_da_prioridade(): void
    {
        $this->assertSame([1, 2, 3], array_map(fn (Prioridade $p) => $p->peso(), [Prioridade::Baixa, Prioridade::Media, Prioridade::Alta]));
    }
}
