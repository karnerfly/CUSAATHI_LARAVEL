<?php

use App\Http\Controllers\Public\ContactMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('admins')->group(base_path('routes/admin.php'));
Route::prefix('newsletter')->group(base_path('routes/newsletter.php'));

// public routes
Route::post('/contact', [ContactMessageController::class, 'store']);
