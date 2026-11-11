<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_cadastra_usuario_e_retorna_token(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Maria',
            'email' => 'maria@teste.com',
            'password' => 'segredo1',
            'password_confirmation' => 'segredo1',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonMissingPath('user.password');
        $this->assertDatabaseHas('users', ['email' => 'maria@teste.com']);
    }

    public function test_nao_cadastra_email_repetido_nem_senha_diferente(): void
    {
        User::factory()->create(['email' => 'maria@teste.com']);

        $this->postJson('/api/register', [
            'name' => 'Maria',
            'email' => 'maria@teste.com',
            'password' => 'segredo1',
            'password_confirmation' => 'outra123',
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_com_credenciais_validas_retorna_token_que_acessa_a_api(): void
    {
        $user = User::factory()->create(['email' => 'maria@teste.com', 'password' => 'segredo1']);

        $response = $this->postJson('/api/login', [
            'email' => 'maria@teste.com',
            'password' => 'segredo1',
        ]);

        $response->assertOk()
            ->assertJsonStructure(['user' => ['id', 'name', 'email'], 'token'])
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonMissingPath('user.password');

        $this->withToken($response->json('token'))
            ->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('email', 'maria@teste.com');
    }

    public function test_login_exige_email_e_senha(): void
    {
        $this->postJson('/api/login', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_login_com_email_inexistente_retorna_401(): void
    {
        $this->postJson('/api/login', [
            'email' => 'ninguem@teste.com',
            'password' => 'segredo1',
        ])->assertUnauthorized()
            ->assertJsonPath('erro', 'Usuário ou senha incorreto');
    }

    public function test_usuario_do_seeder_consegue_logar(): void
    {
        $this->seed();

        $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => '123456',
        ])->assertOk()->assertJsonStructure(['token']);
    }

    public function test_login_com_senha_errada_retorna_401(): void
    {
        User::factory()->create(['email' => 'maria@teste.com', 'password' => 'segredo1']);

        $this->postJson('/api/login', [
            'email' => 'maria@teste.com',
            'password' => 'errada',
        ])->assertUnauthorized();
    }

    public function test_rotas_protegidas_exigem_token(): void
    {
        $this->getJson('/api/tarefas')->assertUnauthorized();
        $this->getJson('/api/categorias')->assertUnauthorized();
        $this->getJson('/api/user')->assertUnauthorized();
        // sem header Accept também responde 401 em JSON
        $this->get('/api/tarefas')->assertUnauthorized()->assertJsonPath('message', 'Unauthenticated.');
    }

    public function test_logout_revoga_o_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('api-token')->plainTextToken;

        $this->withToken($token)->postJson('/api/logout')->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
