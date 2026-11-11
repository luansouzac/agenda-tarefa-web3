<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\TarefaController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, "register"]);
Route::post('login', [AuthController::class, "login"]);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('user', [AuthController::class, "me"]);
    Route::post('logout', [AuthController::class, "logout"]);
    Route::apiResource('categorias', CategoriaController::class);
    Route::apiResource('tarefas', TarefaController::class);
});
