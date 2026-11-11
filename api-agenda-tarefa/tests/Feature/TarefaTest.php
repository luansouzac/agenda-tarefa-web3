<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TarefaTest extends TestCase
{
    use RefreshDatabase;

    private function criarTarefa(User $user, Categoria $categoria): Tarefa
    {
        return Tarefa::create([
            'idusuario' => $user->id,
            'idcategoria' => $categoria->idcategoria,
            'descricao' => 'Tarefa de '.$user->name,
            'data' => '2026-11-20 10:00:00',
            'cumprida' => false,
        ]);
    }

    public function test_usuario_ve_e_altera_apenas_as_proprias_tarefas(): void
    {
        $categoria = Categoria::create(['descricao' => 'Trabalho']);
        $eu = User::factory()->create();
        $outro = User::factory()->create();
        $minha = $this->criarTarefa($eu, $categoria);
        $dele = $this->criarTarefa($outro, $categoria);

        Sanctum::actingAs($eu);

        $this->getJson('/api/tarefas')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonPath('0.idtarefa', $minha->idtarefa);

        $this->deleteJson("/api/tarefas/{$dele->idtarefa}")->assertNotFound();
        $this->assertDatabaseHas('tarefas', ['idtarefa' => $dele->idtarefa]);
    }

    public function test_cria_tarefa_vinculada_ao_usuario_logado(): void
    {
        $categoria = Categoria::create(['descricao' => 'Trabalho']);
        $eu = User::factory()->create();
        $outro = User::factory()->create();

        Sanctum::actingAs($eu);

        $this->postJson('/api/tarefas', [
            'idusuario' => $outro->id,
            'idcategoria' => $categoria->idcategoria,
            'descricao' => 'Estudar Sanctum',
            'data' => '2026-11-20',
        ])->assertCreated()
            ->assertJsonPath('idusuario', $eu->id)
            ->assertJsonPath('categoria.descricao', 'Trabalho');
    }
}
