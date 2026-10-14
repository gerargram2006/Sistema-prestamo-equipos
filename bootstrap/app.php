<?php

use App\Http\Middleware\CheckRole;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Alias para proteger las rutas por rol: 'rol:encargado' / 'rol:estudiante'.
        $middleware->alias([
            'rol' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        // Respaldo global: si la base de datos falla, mostramos una vista amigable
        // sin depender de la sesión (que también fallaría con SESSION_DRIVER=database).
        $exceptions->render(function (QueryException $e, Request $request) {
            report($e);

            return response()->view('errors.base-datos', [], 500);
        });
    })->create();
