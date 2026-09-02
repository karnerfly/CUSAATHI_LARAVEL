<?php

use App\Http\Controllers\Newsletter\NewsLetterSubscriptionController;
use App\Http\Controllers\Newsletter\NewsletterPreferenceController;
use App\Http\Controllers\Newsletter\NewsletterTrackingController;
use Illuminate\Support\Facades\Route;

Route::get('topics', [NewsLetterSubscriptionController::class, 'topics']);

Route::post('subscribe', [NewsletterSubscriptionController::class, 'subscribe']);
Route::post('verify/{token}', [NewsletterSubscriptionController::class, 'verify']);
Route::get('subscription/{subscriber:unsubscribe_token}', [NewsletterSubscriptionController::class, 'subscription']);
Route::post('unsubscribe/{subscriber:unsubscribe_token}', [
    NewsletterSubscriptionController::class,
    'unsubscribe',
])->name('api.newsletter.unsubscribe');

Route::get('preferences/{subscriber:unsubscribe_token}', [NewsletterPreferenceController::class, 'show']);
Route::patch('preferences/{subscriber:unsubscribe_token}', [NewsletterPreferenceController::class, 'update']);

Route::post('track/click', [NewsletterTrackingController::class, 'trackClick']);
Route::get('track/open/{log_id}', [NewsletterTrackingController::class, 'trackOpen']);
