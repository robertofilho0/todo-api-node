<?php

namespace Database\Seeders;

use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Usuário de demonstração: demo@todo.dev / Senha123
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Usuário Demo',
            'email' => 'demo@todo.dev',
            'password' => 'Senha123',
        ]);

        Tarefa::factory(12)->for($user)->create();
    }
}
