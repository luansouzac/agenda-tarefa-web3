<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    private function regras(){
        return [
            'idcategoria' => 'required|exists:categorias,idcategoria',
            'descricao' => 'required|string',
            'data' => 'required|date',
            'cumprida' => 'boolean'
        ];
    }

    //Busca apenas tarefas do usuário logado
    private function buscar(Request $request, string $id){
        return Tarefa::with('categoria')
            ->where('idusuario', $request->user()->id)
            ->find($id);
    }

    public function index(Request $request)
    {
        return response()->json(
            Tarefa::with('categoria')
                ->where('idusuario', $request->user()->id)
                ->orderBy('data')
                ->get(), 200);
    }

    public function store(Request $request)
    {
        $dados = $request->validate($this->regras());
        $dados['idusuario'] = $request->user()->id;
        $dados['cumprida'] = $dados['cumprida'] ?? false;

        $item = Tarefa::create($dados);
        $item->load(['categoria']);
        return response()->json($item, 201);
    }

    public function show(Request $request, string $id)
    {
        $item = $this->buscar($request, $id);
        if($item){
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function update(Request $request, string $id)
    {
        $item = $this->buscar($request, $id);
        if($item){
            $item->update($request->validate($this->regras()));
            $item->load('categoria');
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function destroy(Request $request, string $id)
    {
        $item = $this->buscar($request, $id);
        if($item){
            $item->delete();
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }
}
