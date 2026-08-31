<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Middleware\EnsureAdmin;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('logout', 'logout');
    });

Route::controller(PermissionController::class)
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

Route::controller(DashboardController::class)
    ->middleware(['auth:sanctum', EnsureAdmin::class])
    ->group(function () {
        Route::get('me', 'get_current_admin');
        Route::post('change-password', 'change_password');
        Route::patch('name', 'change_name');
        Route::put('picture', 'upload_profile_picture');
    });

Route::controller(ManagementController::class)
    ->middleware(['auth:sanctum', EnsureAdmin::class])
    ->group(function () {
        Route::get('', 'get_all_admins')->can('read:admin');
        Route::post('', 'create_admin')->can('create:admin');
        Route::post('{admin}/activate', 'activate_admin')->can('activate:admin');
        Route::post('{admin}/deactivate', 'deactivate_admin')->can('deactivate:admin');
    });
