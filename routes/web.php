<?php

use App\Http\Controllers\Api\Budget\BudgetController;
use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Goal\GoalController;
use App\Http\Controllers\Api\Profile\ProfileController;
use App\Http\Controllers\Api\Report\ReportController;
use App\Http\Controllers\Api\Transaction\TransactionController;
use App\Http\Controllers\Api\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Auth::routes();

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::resource('transactions', TransactionController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('budgets', BudgetController::class);
    Route::resource('goals', GoalController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('profile', [UserController::class, 'profile'])->name('user.profile');
    Route::post('profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::post('profile/photo', [UserController::class, 'updateProfilePhoto'])->name('user.profile-photo.update');
    Route::delete('profile/photo', [UserController::class, 'deleteProfilePhoto'])->name('user.profile-photo.delete');
    Route::get('settings', [UserController::class, 'settings'])->name('user.settings');
    Route::post('settings', [UserController::class, 'updateSettings'])->name('user.settings.update');
});

require __DIR__.'/auth.php';
