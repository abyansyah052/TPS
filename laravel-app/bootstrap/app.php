<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Security middleware
        $middleware->web([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);
        
        // File upload security for specific routes
        $middleware->alias([
            'file.upload.security' => \App\Http\Middleware\FileUploadSecurity::class,
        ]);
        
        // Rate limiting aliases
        $middleware->alias([
            'throttle.api' => 'throttle:60,1',
            'throttle.uploads' => 'throttle:5,1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
