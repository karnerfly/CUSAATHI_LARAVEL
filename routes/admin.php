<?php

use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ManagementController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('logout', 'logout');
    });

Route::controller(DashboardController::class)
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('me', 'get_current_admin');
        Route::get('sessions', 'get_sessions');
        Route::delete('sessions/{session}', 'delete_session');
        Route::post('change-password', 'change_password');
        Route::patch('name', 'change_name');
        Route::put('picture', 'upload_profile_picture');
    });

Route::controller(PermissionController::class)
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
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

Route::controller(ManagementController::class)
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'get_all_admins')->can('read:admin');
        Route::post('', 'create_admin')->can('create:admin');

        Route::post('{admin}/activate', 'activate_admin')->can('activate:admin');
        Route::post('{admin}/deactivate', 'deactivate_admin')->can('deactivate:admin');

        Route::get('{admin}/sessions', 'get_admin_sessions')->can('read:admin-session');
        Route::delete('{admin}/sessions/{session}', 'revoke_admin_session')->can('revoke:admin-session');
        Route::post('{admin}/sessions/{session}/restore', 'restore_admin_session')->can('restore:admin-session');

        Route::delete('{admin}', 'delete_admin')->can('delete:admin');
        Route::post('{admin}/restore', 'restore_admin')->withTrashed()->can('restore:admin');
    });

Route::controller(AuditController::class)
    ->prefix('audits')
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index')->can('read:audit');
        Route::get('{audit}', 'show')->can('read:audit');
    });
