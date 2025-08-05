<?php

use App\Http\Controllers\Api\Budget\BudgetController as ApiBudgetController;
use App\Http\Controllers\Api\Category\CategoryController as ApiCategoryController;
use App\Http\Controllers\Api\Goal\GoalController as ApiGoalController;
use App\Http\Controllers\Api\Profile\ProfileController as ApiProfileController;
use App\Http\Controllers\Api\Report\ReportController as ApiReportController;
use App\Http\Controllers\Api\Transaction\TransactionController as ApiTransactionController;
use App\Http\Controllers\Api\User\UserController as ApiUserController;
use Illuminate\Support\Facades\Route;


Route::get('/', fn() => view('welcome'))->name('welcome');


Route::middleware(['auth', 'verified'])->get('/dashboard', fn() => view('dashboard'))->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('profile', [ApiUserController::class, 'profile'])->name('user.profile');
    Route::post('profile', [ApiUserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('profile/photo', [ApiUserController::class, 'updateProfilePhoto'])->name('user.profile-photo.update');
    Route::delete('profile/photo', [ApiUserController::class, 'deleteProfilePhoto'])->name('user.profile-photo.delete');
    Route::get('settings', [ApiUserController::class, 'settings'])->name('user.settings');
    Route::post('settings', [ApiUserController::class, 'updateSettings'])->name('user.settings.update');
});


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('transactions', ApiTransactionController::class);
    Route::resource('categories', ApiCategoryController::class);
    Route::resource('budgets', ApiBudgetController::class);
    Route::resource('goals', ApiGoalController::class);
    Route::get('reports', [ApiReportController::class, 'index'])->name('reports.index');
});

require __DIR__.'/auth.php';
