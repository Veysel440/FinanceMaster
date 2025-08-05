<?php

use App\Http\Controllers\Api\Budget\BudgetController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Goal\GoalController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Report\ReportController;
use App\Http\Controllers\Api\Transaction\TransactionController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('transactions', TransactionController::class);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('budgets', BudgetController::class);
    Route::apiResource('goals', GoalController::class);

    Route::get('profile', [UserController::class, 'profile']);
    Route::put('profile', [UserController::class, 'updateProfile']);
    Route::post('profile/photo', [UserController::class, 'updateProfilePhoto']);
    Route::delete('profile/photo', [UserController::class, 'deleteProfilePhoto']);
    Route::get('settings', [UserController::class, 'settings']);
    Route::put('settings', [UserController::class, 'updateSettings']);

    Route::get('reports', [ReportController::class, 'index']);
});
