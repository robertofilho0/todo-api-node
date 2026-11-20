<?php

namespace Database\Factories;

use App\Enums\Prioridade;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tarefa>
 */
class TarefaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'titulo' => fake('pt_BR')->sentence(4),
            'descricao' => fake('pt_BR')->optional()->paragraph(),
            'prioridade' => fake()->randomElement(Prioridade::cases()),
            'data_limite' => fake()->optional()->dateTimeBetween('-5 days', '+30 days'),
        ];
    }
}
