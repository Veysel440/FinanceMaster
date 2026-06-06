<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Writes one structured 'API_REQUEST' entry per HTTP request with duration,
 * status, user_id and a sanitised body. Emits a 'SLOW_REQUEST' warning when
 * the round-trip exceeds 1000ms.
 *
 * Login / register payloads are entirely redacted regardless of field names
 * so a leaked log never reveals credentials even on validation failures.
 */
class ApiLoggingMiddleware
{
    /**
     * Path fragments whose request body must never be logged.
     */
    private const SENSITIVE_PATHS = ['login', 'register', 'password', 'photo'];

    /**
     * Field names whose values are always replaced with [REDACTED].
     */
    private const SENSITIVE_FIELDS = [
        'password',
        'password_confirmation',
        'current_password',
        'token',
        'api_token',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $startedAt = microtime(true);

        /** @var Response $response */
        $response = $next($request);

        $durationMs = (int) ((microtime(true) - $startedAt) * 1000);
        $isSensitive = $this->isSensitivePath($request->path());

        Log::channel('api')->info('API_REQUEST', [
            'request_id'   => app()->bound('request_id') ? app('request_id') : null,
            'method'       => $request->method(),
            'url'          => $request->fullUrl(),
            'route'        => optional($request->route())->getName(),
            'user_id'      => optional($request->user())->id,
            'ip'           => $request->ip(),
            'user_agent'   => $request->userAgent(),
            'status'       => $response->getStatusCode(),
            'duration_ms'  => $durationMs,
            'request_body' => $isSensitive ? '[REDACTED]' : $this->sanitiseBody($request->all()),
        ]);

        if ($durationMs > 1000) {
            Log::channel('api')->warning('SLOW_REQUEST', [
                'request_id'  => app()->bound('request_id') ? app('request_id') : null,
                'url'         => $request->fullUrl(),
                'duration_ms' => $durationMs,
                'user_id'     => optional($request->user())->id,
            ]);
        }

        return $response;
    }

    private function isSensitivePath(string $path): bool
    {
        foreach (self::SENSITIVE_PATHS as $needle) {
            if (str_contains($path, $needle)) {
                return true;
            }
        }
        return false;
    }

    private function sanitiseBody(array $data): array
    {
        foreach (self::SENSITIVE_FIELDS as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = '[REDACTED]';
            }
        }
        return $data;
    }
}
