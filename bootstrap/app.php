<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'tenant' => \App\Http\Middleware\TenantResolver::class,
            'tenant.auth' => \App\Http\Middleware\TenantAuth::class,
            'tenant.sanctum' => \App\Http\Middleware\SanctumTenantMiddleware::class,
        ]);

        // Don't apply tenant middleware globally to web routes
        // We'll apply it selectively in routes/web.php
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Handle CSRF token expired - show custom 419 page with redirect
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, $request) {
            if ($exception instanceof TokenMismatchException && $response->getStatusCode() === 419) {
                // Extract tenant ID from URL path
                $path = $request->path();
                preg_match('/^([A-Z0-9]+)\//', $path, $matches);

                $loginUrl = !empty($matches[1])
                    ? "/{$matches[1]}/login"
                    : '/';

                return response()
                    ->view('errors.419', ['loginUrl' => $loginUrl], 419)
                    ->withCookie(cookie()->forever('_csrf_expired', '1'));
            }

            return $response;
        });
    })->create();
