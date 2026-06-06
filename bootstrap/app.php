<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\RequestIdMiddleware::class,
            \App\Http\Middleware\ApiLoggingMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            // Skip validation + model-not-found noise; these are normal
            // 4xx flows that ApiLoggingMiddleware already records.
            if ($e instanceof \Illuminate\Validation\ValidationException
                || $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
                return;
            }

            Log::channel('error_log')->error('EXCEPTION', [
                'request_id' => app()->bound('request_id') ? app('request_id') : null,
                'exception'  => get_class($e),
                'message'    => $e->getMessage(),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'url'        => optional(request())->fullUrl(),
                'user_id'    => optional(optional(request())->user())->id,
                'trace'      => collect($e->getTrace())
                    ->take(5)
                    ->map(fn ($t) => ($t['file'] ?? '?') . ':' . ($t['line'] ?? '?'))
                    ->toArray(),
            ]);
        });
    })->create();
