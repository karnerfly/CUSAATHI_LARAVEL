<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('forgot-password', [AdminAuthController::class, 'forgot_password']);
    Route::post('reset-password', [AdminAuthController::class, 'reset_password']);
    Route::post('logout', [AdminAuthController::class, 'logout']);
});

Route::prefix('permissions')
    ->middleware(['auth:sanctum', EnsureAdmin::class])
    ->group(function () {
        Route::post('', [AdminPermissionController::class, 'create'])->can('create:permission');
        Route::get('', [AdminPermissionController::class, 'get'])->can('read:permission');
    });

Route::middleware(['auth:sanctum', EnsureAdmin::class])->group(function () {
    Route::get('me', [AdminDashboardController::class, 'get_current_admin']);
});
