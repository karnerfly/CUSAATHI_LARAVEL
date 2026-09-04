<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\College\CollegeController;
use App\Http\Controllers\Admin\College\CollegeImageController;
use App\Http\Controllers\Admin\College\CollegeLocationController;
use App\Http\Controllers\Admin\College\CollegeNoticeController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Newsletter\CampaignController;
use App\Http\Controllers\Admin\Newsletter\SubscriberController;
use App\Http\Controllers\Admin\Newsletter\TopicController;
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

Route::controller(AdminController::class)
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

Route::controller(ContactMessageController::class)
    ->prefix('contact-messages')
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index')->can('read:contact-message');
        Route::get('{message}', 'show')->can('read:contact-message');
        Route::patch('{message}/mark-as', 'mark_as')->can('update:contact-message');
        Route::delete('{message}', 'destroy')->can('delete:contact-message');
    });

Route::prefix('newsletter')
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('topics', [TopicController::class, 'index'])->can('read:newsletter-topic');
        Route::post('topics', [TopicController::class, 'store'])->can('create:newsletter-topic');
        Route::get('topics/{topic}', [TopicController::class, 'show'])->can('read:newsletter-topic');
        Route::put('topics/{topic}', [TopicController::class, 'update'])->can('update:newsletter-topic');
        Route::delete('topics/{topic}', [TopicController::class, 'destroy'])->can('delete:newsletter-topic');

        Route::get('subscribers', [SubscriberController::class, 'index'])->can('read:newsletter-subscriber');
        Route::get('subscribers/{subscriber}', [SubscriberController::class, 'show'])->can(
            'read:newsletter-subscriber',
        );
        Route::put('subscribers/{subscriber}', [SubscriberController::class, 'update'])->can(
            'update:newsletter-subscriber',
        );
        Route::delete('subscribers/{subscriber}', [SubscriberController::class, 'destroy'])->can(
            'delete:newsletter-subscriber',
        );

        Route::get('campaigns', [CampaignController::class, 'index'])->can('read:newsletter');
        Route::post('campaigns', [CampaignController::class, 'store'])->can('create:newsletter');
        Route::get('campaigns/{campaign}/analytics', [CampaignController::class, 'analytics'])->can('read:newsletter');
        Route::get('campaigns/{campaign}', [CampaignController::class, 'show'])->can('read:newsletter');
        Route::put('campaigns/{campaign}', [CampaignController::class, 'update'])->can('update:newsletter');
        Route::delete('campaigns/{campaign}', [CampaignController::class, 'destroy'])->can('delete:newsletter');
        Route::post('campaigns/{campaign}/dispatch', [CampaignController::class, 'dispatch'])->can(
            'dispatch:newsletter',
        );
    });

Route::prefix('colleges')
    ->middleware(['auth:sanctum', 'session.revoked', 'ensure.admin'])
    ->group(function () {
        Route::get('', [CollegeController::class, 'index']);
        Route::post('', [CollegeController::class, 'store']);
        Route::get('/{college}', [CollegeController::class, 'show']);
        Route::put('{college}', [CollegeController::class, 'update']);
        Route::post('{college}/verify', [CollegeController::class, 'verify']);
        Route::post('{college}/unverify', [CollegeController::class, 'unverify']);
        Route::post('{college}/images', [CollegeController::class, 'add_image_to_college']);
        Route::post('{college}/locations', [CollegeController::class, 'add_location_to_college']);
        Route::delete('{college}', [CollegeController::class, 'destroy']);
        Route::post('{college}/restore', [CollegeController::class, 'restore'])->withTrashed();

        Route::get('images/{image}', [CollegeImageController::class, 'show']);
        Route::put('images/{image}', [CollegeImageController::class, 'update']);
        Route::delete('images/{image}', [CollegeImageController::class, 'destroy']);

        Route::get('locations/{location}', [CollegeLocationController::class, 'show']);
        Route::put('locations/{location}', [CollegeLocationController::class, 'update']);

        Route::get('notices', [CollegeNoticeController::class, 'index']);
        Route::post('notices', [CollegeNoticeController::class, 'store']);
        Route::get('notices/{notice}', [CollegeNoticeController::class, 'show']);
        Route::put('notices/{notice}', [CollegeNoticeController::class, 'update']);
        Route::delete('notices/{notice}', [CollegeNoticeController::class, 'destroy']);
        Route::post('notices/{notice}/restore', [CollegeNoticeController::class, 'restore'])->withTrashed();
    });
