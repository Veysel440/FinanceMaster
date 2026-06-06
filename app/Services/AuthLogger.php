<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Records every authentication-relevant event (success, failure, throttle,
 * register, logout) to the 'auth_log' channel with the originating IP and
 * user agent. 30-day retention by default — long enough for SOC reviews.
 */
class AuthLogger
{
    public function __construct(
        protected Request $request
    ) {}

    private function context(array $extra): array
    {
        return array_merge([
            'request_id' => app()->bound('request_id') ? app('request_id') : null,
            'ip'         => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'timestamp'  => now()->toIso8601String(),
        ], $extra);
    }

    public function loginSuccess(int $userId, string $email): void
    {
        Log::channel('auth_log')->info('AUTH_LOGIN_SUCCESS', $this->context([
            'user_id' => $userId,
            'email'   => $email,
        ]));
    }

    public function loginFailed(string $email): void
    {
        Log::channel('auth_log')->warning('AUTH_LOGIN_FAILED', $this->context([
            'email'  => $email,
            'reason' => 'invalid_credentials',
        ]));
    }

    public function loginThrottled(string $email): void
    {
        Log::channel('auth_log')->warning('AUTH_LOGIN_THROTTLED', $this->context([
            'email' => $email,
        ]));
    }

    public function logout(int $userId): void
    {
        Log::channel('auth_log')->info('AUTH_LOGOUT', $this->context([
            'user_id' => $userId,
        ]));
    }

    public function registered(int $userId, string $email): void
    {
        Log::channel('auth_log')->info('AUTH_REGISTERED', $this->context([
            'user_id' => $userId,
            'email'   => $email,
        ]));
    }
}
