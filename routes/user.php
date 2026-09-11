<?php

use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('register', 'register');
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('logout', 'logout');

        Route::get('/email/verify/{id}/{hash}', 'verify_email')
            ->middleware(['signed'])
            ->name('verification.verify');
        Route::post('/email/resend', 'resend_email_verification')
            ->middleware(['auth:sanctum', 'session.revoked', 'ensure.user', 'throttle:6,1'])
            ->name('verification.send');

        Route::get('login/sso/{provider}/redirect', 'social_login_redirect');
        Route::get('login/sso/{provider}/callback', 'social_login_callback');
    });

Route::controller(DashboardController::class)
    ->middleware(['auth:sanctum', 'ensure.user', 'verified'])
    ->group(function () {
        Route::get('me', 'get_current_user');
        Route::get('sessions', 'get_sessions');
        Route::delete('sessions/{session}', 'delete_session');
        Route::post('change-password', 'change_password');
        Route::patch('name', 'change_name');
        Route::put('picture', 'upload_profile_picture');
    });
