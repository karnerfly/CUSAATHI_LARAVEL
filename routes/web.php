<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return sprintf('CuSaathi API Server (%s)', config('app.env'));
});
