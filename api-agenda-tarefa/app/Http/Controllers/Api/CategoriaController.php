<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Tarefa;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    private function validar(Request $request){
        return $request->validate([
            'descricao' => 'required|string|max:255'
        ], [
            'descricao.required' => 'Informe o nome da categoria'
        ]);
    }

    public function index()
    {
        return response()->json(Categoria::all(), 200);
    }

    public function store(Request $request)
    {
        $item = Categoria::create($this->validar($request));
        return response()->json($item, 201);
    }

    public function show(string $id)
    {
        $item = Categoria::find($id);
        if($item){
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function update(Request $request, string $id)
    {
        $item = Categoria::find($id);
        if($item){
            $item->update($this->validar($request));
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function destroy(string $id)
    {
        $item = Categoria::find($id);
        if($item){
            if(Tarefa::where('idcategoria', $id)->exists()){
                return response()->json(
                    ['erro'=>'Categoria possui tarefas e não pode ser excluída'], 422);
            }
            $item->delete();
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }
}
