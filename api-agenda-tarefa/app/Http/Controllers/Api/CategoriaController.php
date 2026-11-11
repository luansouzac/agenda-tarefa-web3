<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        return response()->json(Categoria::all(), 200);
    }

    public function store(Request $request)
    {
        $item = Categoria::create($request->all());
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
            $item->update($request->all());
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }

    public function destroy(string $id)
    {
        $item = Categoria::find($id);
        if($item){
            $item->delete();
            return response()->json($item, 200);
        }
        return response()->json(
            ['erro'=>'Registro não encontrado'], 404);
    }
}
