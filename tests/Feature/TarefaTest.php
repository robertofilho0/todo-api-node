<?php

namespace Tests\Feature;

use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarefaTest extends TestCase
{
    use RefreshDatabase;

    public function test_cria_tarefa_com_valores_padrao_e_etiquetas(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/tarefas', ['titulo' => 'Estudar Laravel', 'etiquetas' => ['Estudos', 'php', 'PHP ']])
            ->assertCreated()
            ->assertJsonPath('data.titulo', 'Estudar Laravel')
            ->assertJsonPath('data.status', 'pendente')
            ->assertJsonPath('data.prioridade', 'media')
            ->assertJsonPath('data.etiquetas', ['estudos', 'php']);
    }

    public function test_valida_campos(): void
    {
        $this->actingAsUser();

        $this->postJson('/api/tarefas', ['status' => 'feito', 'prioridade' => 'urgente', 'data_limite' => '31/12/2026'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['titulo', 'status', 'prioridade', 'data_limite']);
    }

    public function test_conclui_e_reabre_registrando_a_data(): void
    {
        $user = $this->actingAsUser();
        $tarefa = Tarefa::factory()->for($user)->create();

        $this->patchJson("/api/tarefas/{$tarefa->id}", ['status' => 'concluida'])
            ->assertOk()
            ->assertJsonPath('data.status', 'concluida');
        $this->assertNotNull($tarefa->fresh()->concluida_em);

        $this->patchJson("/api/tarefas/{$tarefa->id}", ['status' => 'pendente'])->assertJsonPath('data.concluida_em', null);
    }

    public function test_usuario_nao_acessa_tarefas_de_outro(): void
    {
        $this->actingAsUser();
        $alheia = Tarefa::factory()->for(User::factory())->create();

        $this->getJson("/api/tarefas/{$alheia->id}")->assertNotFound();
        $this->patchJson("/api/tarefas/{$alheia->id}", ['titulo' => 'x'])->assertNotFound();
        $this->deleteJson("/api/tarefas/{$alheia->id}")->assertNotFound();
        $this->getJson('/api/tarefas')->assertJsonCount(0, 'data');
    }

    public function test_remove_tarefa(): void
    {
        $user = $this->actingAsUser();
        $tarefa = Tarefa::factory()->for($user)->create();

        $this->deleteJson("/api/tarefas/{$tarefa->id}")->assertNoContent();
        $this->assertModelMissing($tarefa);
    }

    public function test_filtra_busca_ordena_e_pagina(): void
    {
        $user = $this->actingAsUser();
        Tarefa::factory()->for($user)->create(['titulo' => 'Comprar pão', 'prioridade' => 'baixa']);
        Tarefa::factory()->for($user)->create(['titulo' => 'Pagar contas', 'prioridade' => 'alta', 'status' => 'em_andamento']);
        Tarefa::factory()->for($user)->create(['titulo' => 'Comprar café', 'prioridade' => 'alta', 'status' => 'concluida']);

        $this->getJson('/api/tarefas?status=concluida')->assertJsonPath('data.0.titulo', 'Comprar café');
        $this->getJson('/api/tarefas?busca=comprar')->assertJsonCount(2, 'data');
        $this->getJson('/api/tarefas?ordenar=prioridade&direcao=asc')->assertJsonPath('data.0.titulo', 'Comprar pão');
        $this->getJson('/api/tarefas?per_page=2&page=2')->assertJsonCount(1, 'data')->assertJsonPath('meta.total', 3);
        $this->getJson('/api/tarefas?ordenar=senha')->assertUnprocessable();
    }

    public function test_filtra_por_etiqueta_e_atrasadas(): void
    {
        $user = $this->actingAsUser();
        $this->postJson('/api/tarefas', ['titulo' => 'Relatório', 'etiquetas' => ['trabalho']]);
        $this->postJson('/api/tarefas', ['titulo' => 'Academia', 'etiquetas' => ['saude']]);
        Tarefa::factory()->for($user)->create(['titulo' => 'Atrasada', 'data_limite' => now()->subDays(2)]);

        $this->getJson('/api/tarefas?etiqueta=trabalho')->assertJsonCount(1, 'data')->assertJsonPath('data.0.titulo', 'Relatório');
        $this->getJson('/api/tarefas?atrasadas=1')
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.atrasada', true);

        $this->getJson('/api/etiquetas')
            ->assertJsonPath('data.0.nome', 'saude')
            ->assertJsonPath('data.1.tarefas_count', 1);
    }

    public function test_estatisticas(): void
    {
        $user = $this->actingAsUser();
        Tarefa::factory()->for($user)->create(['status' => 'pendente', 'data_limite' => now()->subDay()]);
        Tarefa::factory()->for($user)->create(['status' => 'em_andamento', 'data_limite' => today()]);
        Tarefa::factory()->for($user)->create(['status' => 'concluida']);
        Tarefa::factory()->for($user)->create(['status' => 'concluida']);
        Tarefa::factory()->for(User::factory())->create();

        $this->getJson('/api/tarefas/estatisticas')
            ->assertOk()
            ->assertExactJson([
                'total' => 4,
                'por_status' => ['pendente' => 1, 'em_andamento' => 1, 'concluida' => 2],
                'atrasadas' => 1,
                'vencem_hoje' => 1,
                'percentual_concluido' => 50.0,
            ]);
    }
}
