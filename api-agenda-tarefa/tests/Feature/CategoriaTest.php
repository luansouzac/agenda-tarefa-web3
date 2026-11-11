<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Tarefa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoriaTest extends TestCase
{
    use RefreshDatabase;

    public function test_categoria_exige_descricao(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/categorias', ['descricao' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['descricao']);
    }

    public function test_nao_exclui_categoria_com_tarefas(): void
    {
        $user = User::factory()->create();
        $categoria = Categoria::create(['descricao' => 'Trabalho']);
        Tarefa::create([
            'idusuario' => $user->id,
            'idcategoria' => $categoria->idcategoria,
            'descricao' => 'Relatório',
            'data' => '2026-11-20',
            'cumprida' => false,
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson("/api/categorias/{$categoria->idcategoria}")
            ->assertStatus(422)
            ->assertJsonPath('erro', 'Categoria possui tarefas e não pode ser excluída');
        $this->assertDatabaseHas('categorias', ['idcategoria' => $categoria->idcategoria]);
    }

    public function test_exclui_categoria_sem_tarefas(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $categoria = Categoria::create(['descricao' => 'Lazer']);

        $this->deleteJson("/api/categorias/{$categoria->idcategoria}")->assertOk();
        $this->assertDatabaseMissing('categorias', ['idcategoria' => $categoria->idcategoria]);
    }
}
