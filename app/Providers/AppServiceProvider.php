<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
        $this->configureSlowQueryLogging();
    }

    /**
     * Log any DB query that takes 100ms or more to the 'api' channel with
     * SQL, bindings, and the originating request_id so it can be linked to
     * the surrounding API_REQUEST entry written by ApiLoggingMiddleware.
     */
    protected function configureSlowQueryLogging(): void
    {
        DB::listen(function ($query) {
            if ($query->time < 100) {
                return;
            }

            Log::channel('api')->warning('SLOW_QUERY', [
                'request_id'  => app()->bound('request_id') ? app('request_id') : null,
                'duration_ms' => $query->time,
                'sql'         => $query->sql,
                'bindings'    => $query->bindings,
                'connection'  => $query->connectionName,
            ]);
        });
    }

    /**
     * Define the rate limiters used across the application.
     * Authenticated users: 120 req/min keyed by user id.
     * Guests: 30 req/min keyed by IP — protects against scraping
     * and helps blunt brute-force attempts across login/register.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });
    }
}
