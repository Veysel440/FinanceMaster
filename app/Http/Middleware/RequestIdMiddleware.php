<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Attaches a UUID X-Request-Id header to every request and response so that
 * log entries written by ApiLoggingMiddleware, FinancialLogger, AuthLogger
 * and the exception handler can be correlated across channels.
 *
 * The id is also bound into the container as 'request_id' so non-HTTP
 * helpers (loggers, listeners) can resolve it without touching the Request.
 */
class RequestIdMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->headers->get('X-Request-Id', (string) Str::uuid());

        $request->headers->set('X-Request-Id', $requestId);
        app()->instance('request_id', $requestId);

        $response = $next($request);

        $response->headers->set('X-Request-Id', $requestId);

        return $response;
    }
}
