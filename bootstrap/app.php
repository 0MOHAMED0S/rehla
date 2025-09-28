<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'author' => \App\Http\Middleware\AuthorRoleMiddleware::class,
            'admin' => \App\Http\Middleware\AdminRoleMiddleware::class,
            'support' => \App\Http\Middleware\SupportRoleMiddleware::class,
            'enhalak' => \App\Http\Middleware\EnhaLakRoleMiddleware::class,
            'check.child' => \App\Http\Middleware\CheckChildOwnership::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
