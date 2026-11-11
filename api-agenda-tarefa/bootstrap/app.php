<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // API não tem página de login: visitante sem token recebe 401
        $middleware->redirectGuestsTo(
            fn ($request) => $request->is('api/*') ? null : '/');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Rotas da API sempre respondem em JSON (ex.: 401 sem token)
        $exceptions->shouldRenderJsonWhen(
            fn ($request) => $request->is('api/*'));
    })->create();
