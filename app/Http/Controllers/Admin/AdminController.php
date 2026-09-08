<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\IndexAdminRegistrationRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Resources\Admin\Session\SessionResource;
use App\Models\Admin;
use App\Models\AdminRegistration;
use App\Models\Session;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Admin Management')]
class AdminController extends Controller
{
    /**
     * Display a listing of all admins.
     */
    public function get_all_admins()
    {
        return Admin::withTrashed()->orderBy('id')->get();
    }

    /**
     * Store a newly created admin in storage.
     */
    public function create_admin(StoreAdminRequest $request)
    {
        $admin = Admin::create($request->validated());
        $admin->refresh();

        return response()->json(
            [
                'message' => 'Admin created.',
                'admin' => $admin,
            ],
            201,
        );
    }

    /**
     * Deactivate the specified admin.
     */
    public function deactivate_admin(Admin $admin)
    {
        $admin->update(['active' => false]);

        return response()->noContent();
    }

    /**
     * Activate the specified admin.
     */
    public function activate_admin(Admin $admin)
    {
        $admin->update(['active' => true]);

        return response()->noContent();
    }

    /**
     * Display specified admin sessions.
     */
    public function get_admin_sessions(Request $request, Admin $admin)
    {
        $sid = $request->session()->getId();
        $sessions = $admin
            ->sessions()
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->getTimestamp())
            ->orderBy('id')
            ->get()
            ->map(function ($session) use ($sid) {
                $session->current = false;
                if ($session->id == $sid) {
                    $session->current = true;
                }

                return $session;
            });

        return SessionResource::collection($sessions);
    }

    /**
     * Revoke specified admin session.
     */
    public function revoke_admin_session(Request $request, Admin $admin, Session $session)
    {
        $session_exists = $admin->sessions()->where('id', $session->id)->exists();
        if (!$session_exists) {
            return response()->json(
                [
                    'message' => 'Session does not belong to this admin.',
                ],
                404,
            );
        }

        $session->revoked = true;
        $session->save();

        return response()->noContent();
    }

    /**
     * Restore specified admin session.
     */
    public function restore_admin_session(Request $request, Admin $admin, Session $session)
    {
        $session_exists = $admin->sessions()->where('id', $session->id)->exists();
        if (!$session_exists) {
            return response()->json(
                [
                    'message' => 'Session does not belong to this admin.',
                ],
                404,
            );
        }

        $session->revoked = false;
        $session->save();

        return response()->noContent();
    }

    /**
     * Remove the specified admin from storage.
     */
    public function delete_admin(Admin $admin)
    {
        $admin->delete();
        $admin->sessions()->delete();

        return response()->noContent();
    }

    /**
     * Restore the specified admin from storage.
     */
    public function restore_admin(Admin $admin)
    {
        $admin->restore();

        return response()->noContent();
    }

    /**
     * Display a listing of admin registrations.
     */
    public function get_registration_requests(IndexAdminRegistrationRequest $request)
    {
        $query = AdminRegistration::latest();

        if ($request->has('status')) {
            match ($request->input('status')) {
                'pending' => $query->whereNull('sent_at'),
                'sent' => $query->whereNotNull('sent_at'),
                'expired' => $query->whereNotNull('expiration')->where('expiration', '<=', now()->timestamp),
                default => null,
            };
        }

        $registrations = $query->get();

        return $registrations;
    }

    /**
     * Send admin registration mail.
     */
    public function send_registration_mail(AdminRegistration $registration)
    {
        $now = now();
        $expiry = $now;
        $expiry->addMinutes(config('app.admin_registration_expiry'));

        $registration->update([
            'expiration' => $expiry->timestamp,
            'sent_at' => $now,
        ]);

        $registration->sendRegistrationMail(config('app.admin_url'));

        return response()->noContent();
    }

    /**
     * Remove the specified admin registration.
     */
    public function delete_registration(AdminRegistration $registration)
    {
        $registration->delete();

        return response()->noContent();
    }
}
