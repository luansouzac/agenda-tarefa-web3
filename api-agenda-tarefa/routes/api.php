<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\TarefaController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [AuthController::class, "login"]);
Route::apiResource('categorias', CategoriaController::class);
Route::apiResource('tarefas', TarefaController::class);