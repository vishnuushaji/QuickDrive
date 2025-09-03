<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\SuperAdminOnly;
use App\Http\Middleware\CheckRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Temporarily remove CSRF from web middleware group for testing
        $middleware->web(remove: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ]);

        // Remove Sanctum stateful middleware from API routes to avoid CSRF issues
        // $middleware->api(prepend: [
        //     \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        // ]);

        // Keep your existing aliases
        $middleware->alias([
            'super_admin' => SuperAdminOnly::class,
            'role' => CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();