<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\IndexAdminRegistrationCampaignRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRegistrationCampaignRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Resources\Admin\Admin\AdminRegistrationCampaignResource;
use App\Http\Resources\Admin\Session\SessionResource;
use App\Models\Admin;
use App\Models\AdminRegistration;
use App\Models\AdminRegistrationCampaign;
use App\Models\Session;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Admin Management')]
class AdminController extends Controller
{
    /**
     * Display a listing of all admins.
     */
    public function index_admins()
    {
        return Admin::withTrashed()->orderBy('id')->get();
    }

    /**
     * Store a newly created admin in storage.
     */
    public function store_admin(StoreAdminRequest $request)
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
    public function index_admin_sessions(Request $request, Admin $admin)
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
        if (! $session_exists) {
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
        if (! $session_exists) {
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
    public function destroy_admin(Admin $admin)
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
     * Display a listing of admin registration campaigns.
     */
    public function index_registration_campaigns(IndexAdminRegistrationCampaignRequest $request)
    {
        $query = AdminRegistrationCampaign::query()->latest();

        $query->when(
            $request->has('status'),
            fn ($query) => match ($request->input('status')) {
                'expired' => $query->whereNotNull('expires_at')->where('expires_at', '<=', now()),
                'active' => $query->where('active', true)->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                }),
                'deactive' => $query->where('active', false)->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
                }),
                default => null,
            },
        );

        // $query->when($request->has('admin_id'), fn($query) => $query->where('admin_id', $request->admin_id));

        $campaigns = $query->get();

        return $campaigns;
    }

    /**
     * Store a newly created admin registration campaign in storage.
     */
    public function store_registration_campaign(StoreAdminRegistrationCampaignRequest $request)
    {
        $validated = $request->validated();
        $admin_id = $request->user('admin')->id;
        $expires_at = now()->plus(hours: $validated['expiry']);

        $campaign = AdminRegistrationCampaign::create([
            'admin_id' => $admin_id,
            'expires_at' => $expires_at,
        ]);
        $campaign->refresh();

        return AdminRegistrationCampaignResource::make($campaign);
    }

    /**
     * Display the specified admin registration campaign.
     */
    public function show_registration_campaign(AdminRegistrationCampaign $campaign)
    {
        $campaign->load(['admin', 'registrations']);

        return AdminRegistrationCampaignResource::make($campaign);
    }

    /**
     * Deactivate the specified admin registration campaign.
     */
    public function deactivate_registration_campaign(AdminRegistrationCampaign $campaign)
    {
        $campaign->update([
            'active' => false,
        ]);

        return response()->noContent();
    }

    /**
     * Activate the specified admin registration campaign.
     */
    public function activate_registration_campaign(AdminRegistrationCampaign $campaign)
    {
        $campaign->update([
            'active' => true,
        ]);

        return response()->noContent();
    }

    /**
     * Remove the specified admin registration campaign from storage.
     */
    // public function destroy_registration_campaign(AdminRegistrationCampaign $campaign)
    // {
    //     $campaign->delete();

    //     return response()->noContent();
    // }

    /**
     * Send admin registration mail.
     */
    public function send_registration_mail(AdminRegistration $registration)
    {
        if (! $registration->campaign()->first()->active()) {
            return response()->json(
                [
                    'message' => 'Registration campaign is expired or not active.',
                ],
                422,
            );
        }

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
