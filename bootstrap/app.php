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
        // Add security headers to all responses
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);

        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'auth.custom' => \App\Http\Middleware\EnsureUserIsAuthenticated::class,
            'check.shift.time' => \App\Http\Middleware\CheckShiftTime::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'rate_limit' => \App\Http\Middleware\RateLimitRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // Redirect ke halaman login
            return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
        });

        // Handle Authorization Exception (403 Forbidden)
        $exceptions->render(function (\Illuminate\Auth\Access\AuthorizationException $e, \Illuminate\Http\Request $request) {
            // Return custom 403 view dengan SweetAlert notifikasi
            return response()->view('errors.403', [], 403);
        });
    })->create();
