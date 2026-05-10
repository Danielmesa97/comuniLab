<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
//use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            return response()->json([
                'message' => 'No autenticado'
            ], 401);
        });
    })

    ->withMiddleware(function ($middleware) {
        $middleware->alias([
            'superadmin' => \App\Http\Middleware\SuperAdminMiddleware::class,
        ]);
    })

    ->withMiddleware(function (Middleware $middleware): void {
//        // 🔥 AÑADE ESTO (MUY IMPORTANTE)
//        $middleware->append(EnsureFrontendRequestsAreStateful::class);
//
//        // ya lo tenías
        $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
    })
    ->create();

