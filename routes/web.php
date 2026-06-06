<?php

use Illuminate\Support\Facades\Route;

// Minimal landing endpoint. The Next.js SPA lives at a separate origin
// (http://localhost:3000 in dev) and consumes /api/* exclusively. The
// web layer keeps only the root health response and the framework
// health route registered in bootstrap/app.php (/up).
Route::get('/', fn () => response()->json([
    'app'      => config('app.name'),
    'frontend' => 'https://github.com/Veysel440/FinanceMaster (see finance-master-frontend/)',
    'api'      => url('/api'),
]))->name('welcome');
