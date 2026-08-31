<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPermissionController;
use App\Http\Middleware\EnsureAdmin;

Route::prefix('admins')->group(function () {
    Route::controller(AdminAuthController::class)
        ->prefix('auth')
        ->group(function () {
            Route::post('login', 'login');
            Route::post('forgot-password', 'forgot_password');
            Route::post('reset-password', 'reset_password');
            Route::post('logout', 'logout');
        });

    Route::controller(AdminPermissionController::class)
        ->middleware(['auth:sanctum', EnsureAdmin::class])
        ->group(function () {
            Route::get('permissions', 'index')->can('read:permission');
            Route::get('permissions/{permission}', 'show')->can('read:permission');
            Route::post('permissions', 'store')->can('create:permission');
            Route::put('permissions/{permission}', 'update')->can('update:permission');
            Route::delete('permissions/{permission}', 'destroy')->can('delete:permission');
            Route::post('{admin}/permissions', 'assign')->can('assign:permission');
            Route::get('{admin}/permissions', 'get_admin_permissions')->can('read:permission');
            Route::delete('{admin}/permissions/{permission}', 'revoke')->can('revoke:permission');
        });

    Route::controller(AdminDashboardController::class)
        ->middleware(['auth:sanctum', EnsureAdmin::class])
        ->group(function () {
            Route::get('me', 'get_current_admin');
            Route::post('change-password', 'change_password');
            Route::patch('name', 'change_name');
            Route::put('picture', 'upload_profile_picture');
        });
});
