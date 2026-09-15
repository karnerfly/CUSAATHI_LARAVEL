<?php

use App\Http\Controllers\User\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return sprintf('CuSaathi API Server (%s)', config('app.env'));
});

Route::get('api/v2/users/auth/login/sso/{provider}/callback', [AuthController::class, 'social_login_callback']);
