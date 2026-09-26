<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\College\CollegeController;
use App\Http\Controllers\Admin\College\CollegeImageController;
use App\Http\Controllers\Admin\College\CollegeLocationController;
use App\Http\Controllers\Admin\College\CourseController;
use App\Http\Controllers\Admin\College\CourseTypeController;
use App\Http\Controllers\Admin\College\FacilityController;
use App\Http\Controllers\Admin\College\StreamController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\Newsletter\CampaignController;
use App\Http\Controllers\Admin\Newsletter\SubscriberController;
use App\Http\Controllers\Admin\Newsletter\TopicController;
use App\Http\Controllers\Admin\NoticeController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Support\Facades\Route;

Route::controller(AuthController::class)
    ->prefix('auth')
    ->group(function () {
        Route::post('login', 'login');
        Route::post('forgot-password', 'forgot_password');
        Route::post('reset-password', 'reset_password');
        Route::post('registration/request', 'registration_request')
            ->middleware('signed')
            ->name('api.admin.registration');
        Route::get('registration/status', 'registration_status');
        Route::post('registration/complete', 'complete_registration_request');
        Route::post('logout', 'logout');
    });

Route::controller(DashboardController::class)
    ->prefix('account')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('me', 'get_current_admin');
        Route::get('sessions', 'get_sessions');
        Route::post('change-password', 'change_password');
        Route::patch('name', 'change_name');
        Route::put('picture', 'upload_profile_picture');
        Route::delete('sessions/{session}', 'delete_session');
    });

Route::controller(AuditController::class)
    ->prefix('audits')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index')->can('read:audit');
        Route::get('{audit}', 'show')->can('read:audit');
    });

Route::controller(ContactMessageController::class)
    ->prefix('contact-messages')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index')->can('read:contact-message');
        Route::patch('{message}/mark-as', 'mark_as')->can('update:contact-message');
        Route::delete('{message}', 'destroy')->can('delete:contact-message');
        Route::get('{message}', 'show')->can('read:contact-message');
    });

Route::prefix('newsletter')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::controller(TopicController::class)
            ->prefix('topics')
            ->group(function () {
                Route::get('', 'index')->can('read:newsletter-topic');
                Route::post('', 'store')->can('create:newsletter-topic');
                Route::put('{topic}', 'update')->can('update:newsletter-topic');
                Route::delete('{topic}', 'destroy')->can('delete:newsletter-topic');
                Route::get('{topic}', 'show')->can('read:newsletter-topic');
            });

        Route::controller(SubscriberController::class)
            ->prefix('subscribers')
            ->group(function () {
                Route::get('', 'index')->can('read:newsletter-subscriber');
                Route::put('{subscriber}', 'update')->can('update:newsletter-subscriber');
                Route::delete('{subscriber}', 'destroy')->can('delete:newsletter-subscriber');
                Route::get('{subscriber}', 'show')->can('read:newsletter-subscriber');
            });

        Route::controller(CampaignController::class)
            ->prefix('campaigns')
            ->group(function () {
                Route::get('', 'index')->can('read:newsletter');
                Route::post('', 'store')->can('create:newsletter');
                Route::get('{campaign}/analytics', 'analytics')->can('read:newsletter');
                Route::put('{campaign}', 'update')->can('update:newsletter');
                Route::delete('{campaign}', 'destroy')->can('delete:newsletter');
                Route::post('{campaign}/dispatch', 'dispatch')->can('dispatch:newsletter');
                Route::get('{campaign}', 'show')->can('read:newsletter');
            });
    });

Route::controller(NoticeController::class)
    ->prefix('notices')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index')->can('read:notice');
        Route::post('', 'store')->can('create:notice');
        Route::put('{notice}', 'update')->can('update:notice');
        Route::delete('{notice}', 'destroy')->can('delete:notice');
        Route::post('{notice}/restore', 'restore')->withTrashed()->can('restore:notice');
        Route::get('{notice}', 'show')->can('read:notice');
    });

Route::prefix('colleges')
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::controller(CourseTypeController::class)
            ->prefix('course-types')
            ->group(function () {
                Route::get('', 'index')->can('read:course-type');
                Route::post('', 'store')->can('create:course-type');
                Route::put('{type}', 'update')->can('update:course-type');
                Route::delete('{type}', 'destroy')->can('delete:course-type');
                Route::get('{type}', 'show')->can('read:course-type');
            });

        Route::controller(CourseController::class)
            ->prefix('courses')
            ->group(function () {
                Route::get('', 'index')->can('read:course');
                Route::post('', 'store')->can('create:course');
                Route::put('{course}', 'update')->can('update:course');
                Route::delete('{course}', 'destroy')->can('delete:course');
                Route::get('{course}', 'show')->can('read:course');
            });

        Route::controller(StreamController::class)
            ->prefix('streams')
            ->group(function () {
                Route::get('', 'index')->can('read:stream');
                Route::post('', 'store')->can('create:stream');
                Route::put('{stream}', 'update')->can('update:stream');
                Route::delete('{stream}', 'destroy')->can('delete:stream');
                Route::get('{stream}', 'show')->can('read:stream');
            });

        Route::controller(FacilityController::class)
            ->prefix('facilities')
            ->group(function () {
                Route::get('', 'index')->can('read:facility');
                Route::post('', 'store')->can('create:facility');
                Route::put('{facility}', 'update')->can('update:facility');
                Route::delete('{facility}', 'destroy')->can('delete:facility');
                Route::get('{facility}', 'show')->can('read:facility');
            });

        Route::controller(CollegeImageController::class)
            ->prefix('images')
            ->group(function () {
                Route::get('{image}', 'show')->can('read:college-image');
                Route::put('{image}', 'update')->can('update:college-image');
                Route::delete('{image}', 'destroy')->can('delete:college-image');
            });

        Route::controller(CollegeLocationController::class)
            ->prefix('locations')
            ->group(function () {
                Route::get('{location}', 'show')->can('read:college-location');
                Route::put('{location}', 'update')->can('update:college-location');
            });

        Route::controller(CollegeController::class)->group(function () {
            Route::get('', 'index')->can('read:college');
            Route::post('', 'store')->can('create:college');
            Route::put('{college}', 'update')->can('update:college');
            Route::put('{college}/thumbnail', 'upload_thumbnail')->can('update:college');
            Route::delete('{college}', 'destroy')->can('delete:college');
            Route::post('{college}/restore', 'restore')->withTrashed()->can('restore:college');
            Route::post('{college}/verify', 'verify')->can('verify:college');
            Route::post('{college}/unverify', 'unverify')->can('unverify:college');
            Route::post('{college}/images', 'add_images_to_college')->can('create:college-image');
            Route::post('{college}/locations', 'add_location_to_college')->can('create:college-location');
            Route::post('{college}/facilities', 'add_facility_to_college')->can('map:college-facility');
            Route::delete('{college}/facilities', 'remove_facility_from_college')->can('unmap:college-facility');
            Route::post('{college}/streams', 'add_stream_to_college')->can('add:college-stream');
            Route::put('college-stream/{college_stream}', 'update_college_stream')->can('update:college-stream');
            Route::get('{college}', 'show')->can('read:college');
        });
    });

Route::controller(PermissionController::class)
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('permissions', 'index')->can('read:permission');
        Route::post('permissions', 'store')->can('create:permission');

        Route::get('permissions/{permission}', 'show')->can('read:permission');
        Route::put('permissions/{permission}', 'update')->can('update:permission');
        Route::delete('permissions/{permission}', 'destroy')->can('delete:permission');

        Route::post('{admin}/permissions', 'assign')->can('assign:permission');
        Route::get('{admin}/permissions', 'get_admin_permissions')->can('read:permission');
        Route::delete('{admin}/permissions/{permission}', 'revoke')->can('revoke:permission');
    });

Route::controller(AdminController::class)
    ->middleware(['auth:sanctum', 'ensure.admin'])
    ->group(function () {
        Route::get('', 'index_admins')->can('read:admin');
        Route::post('', 'store_admin')->can('create:admin');

        Route::get('registrations/campaigns', 'index_registration_campaigns')->can('read:admin-registration-campaign');
        Route::post('registrations/campaigns', 'store_registration_campaign')->can(
            'create:admin-registration-campaign',
        );
        Route::get('registrations/campaigns/{campaign}', 'show_registration_campaign')->can(
            'read:admin-registration-campaign',
        );
        Route::post('registrations/campaigns/{campaign}/deactivate', 'deactivate_registration_campaign')->can(
            'deactivate:admin-registration-campaign',
        );
        Route::post('registrations/campaigns/{campaign}/activate', 'activate_registration_campaign')->can(
            'activate:admin-registration-campaign',
        );

        Route::post('registrations/{registration}/send', 'send_registration_mail')->can('send:admin-registration-mail');
        Route::delete('registrations/{registration}', 'delete_registration')->can('delete:admin-registration-request');

        Route::post('{admin}/activate', 'activate_admin')->can('activate:admin');
        Route::post('{admin}/deactivate', 'deactivate_admin')->can('deactivate:admin');

        Route::get('{admin}/sessions', 'index_admin_sessions')->can('read:admin-session');
        Route::delete('{admin}/sessions/{session}', 'revoke_admin_session')->can('revoke:admin-session');
        Route::post('{admin}/sessions/{session}/restore', 'restore_admin_session')->can('restore:admin-session');

        Route::delete('{admin}', 'destroy_admin')->can('delete:admin');
        Route::post('{admin}/restore', 'restore_admin')->withTrashed()->can('restore:admin');
        Route::get('{admin}', 'show_admin')->can('read:admin');
    });
