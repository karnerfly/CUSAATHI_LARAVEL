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
        Route::get('', [AdminPermissionController::class, 'index'])->can('read:permission');
        Route::get('/{id}', [AdminPermissionController::class, 'show'])->can('read:permission');
        Route::post('', [AdminPermissionController::class, 'store'])->can('create:permission');
        Route::put('/{id}', [AdminPermissionController::class, 'update'])->can('update:permission');
        Route::delete('/{id}', [AdminPermissionController::class, 'destroy'])->can('delete:permission');
        Route::post('/assign', [AdminPermissionController::class, 'assign'])->can('assign:permission');
        Route::post('/revoke', [AdminPermissionController::class, 'revoke'])->can('revoke:permission');
    });

Route::middleware(['auth:sanctum', EnsureAdmin::class])->group(function () {
    Route::get('me', [AdminDashboardController::class, 'get_current_admin']);
});
