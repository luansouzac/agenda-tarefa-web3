<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
   public function index()
    {
        return response()->json(
            Tarefa::with(['categoria', 
                'user'])->get(), 200);
    }

    public function store(Request $request)
    {
        $item = Tarefa::create($request->all());
        $item->load(['categoria']);
        return response()->json($item, 201);
    }

    public function show(string $id)
    {
        $item = Tarefa::find($id)::with('categoria')->get();
        if($item){
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function update(Request $request, string $id)
    {
        $item = Tarefa::with('categoria')->get()->find($id);
        if($item){
            $item->update($request->all());
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function destroy(string $id)
    {
        $item = Tarefa::find($id);
        if($item){
            $item->delete();
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }
}
