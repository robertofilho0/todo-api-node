<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastra_e_retorna_token(): void
    {
        $this->postJson('/api/register', ['name' => 'Ana', 'email' => 'ANA@x.com', 'password' => 'Senha123'])
            ->assertCreated()
            ->assertJsonStructure(['access_token', 'token_type', 'user' => ['id', 'name', 'email']])
            ->assertJsonPath('user.email', 'ana@x.com')
            ->assertJsonMissingPath('user.password');
    }

    public function test_valida_cadastro(): void
    {
        User::factory()->create(['email' => 'ana@x.com']);

        $this->postJson('/api/register', ['name' => 'A', 'email' => 'ana@x.com', 'password' => '123'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_e_logout(): void
    {
        $user = User::factory()->create(['email' => 'ana@x.com', 'password' => 'Senha123']);

        $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'errada'])->assertUnauthorized();
        $token = $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'Senha123'])
            ->assertOk()
            ->json('access_token');

        $this->withToken($token)->getJson('/api/me')->assertOk()->assertJsonPath('data.email', 'ana@x.com');
        $this->withToken($token)->postJson('/api/logout')->assertNoContent();
        $this->assertSame(0, $user->tokens()->count());
    }

    public function test_bloqueia_apos_varias_tentativas(): void
    {
        User::factory()->create(['email' => 'ana@x.com']);

        foreach (range(1, 5) as $_) {
            $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'errada']);
        }

        $this->postJson('/api/login', ['email' => 'ana@x.com', 'password' => 'errada'])->assertTooManyRequests();
    }

    public function test_rotas_exigem_autenticacao(): void
    {
        $this->getJson('/api/tarefas')->assertUnauthorized();
        $this->getJson('/api/nada')->assertNotFound()->assertJsonPath('message', 'Rota não encontrada.');
    }
}
