<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        // health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.token' => \App\Http\Middleware\ValidateApiToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $e, $request) {
            // Return JSON responses for API routes only
            if ($request->expectsJson() || $request->is('api/*')) {
                // Don't expose sensitive error details in production
                $isDebug = config('app.debug');

                // Skip exceptions that are already handled (ValidationException, AuthenticationException)
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return null; // Let Laravel handle validation exceptions
                }

                if ($e instanceof \Illuminate\Auth\AuthenticationException) {
                    return null; // Let Laravel handle authentication exceptions
                }

                // Handle database/query exceptions
                if ($e instanceof \Illuminate\Database\QueryException) {
                    Log::error('Database error', [
                        'message' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    return response()->json([
                        'message' => 'Database error occurred',
                        'error' => $isDebug ? $e->getMessage() : null,
                    ], 500);
                }

                // Generic exception handler for unhandled exceptions
                Log::error('Unhandled exception', [
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'trace' => $e->getTraceAsString(),
                ]);

                return response()->json([
                    'message' => $isDebug ? $e->getMessage() : 'Server error occurred',
                ], 500);
            }

            return null; // Let Laravel handle non-API exceptions
        });
    })->create();
