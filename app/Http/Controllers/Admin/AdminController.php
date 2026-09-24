<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Admin\IndexAdminRegistrationCampaignRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRegistrationCampaignRequest;
use App\Http\Requests\Admin\Admin\StoreAdminRequest;
use App\Http\Resources\Admin\Admin\AdminDetailResource;
use App\Http\Resources\Admin\Admin\AdminRegistrationCampaignResource;
use App\Http\Resources\Session\SessionResource;
use App\Models\Admin;
use App\Models\AdminRegistration;
use App\Models\AdminRegistrationCampaign;
use App\Models\Session;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Display the specified admin.
     */
    public function show_admin(Request $request, Admin $admin)
    {
        $sid = $request->session()->getId();

        /** @var \Illuminate\Auth\SessionGuard $admin_guard */
        $admin_guard = Auth::guard('admin');
        $admin_auth_key = $admin_guard->getName();

        $sessions = $admin
            ->sessions()
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->getTimestamp())
            ->orderBy('id')
            ->get()
            ->filter(function ($session) use ($admin_auth_key, $admin) {
                $payload = json_decode(base64_decode($session->payload), true);
                return isset($payload[$admin_auth_key]) && $payload[$admin_auth_key] == $admin->id;
            })
            ->map(function ($session) use ($sid) {
                $session->current = $session->id === $sid;
                $payload = json_decode(base64_decode($session->payload), true);
                $session->revoked = $payload['admin_revoked'] ?? false;
                return $session;
            })
            ->values();

        $admin->setRelation('sessions', $sessions);
        $admin->load('permissions');

        return AdminDetailResource::make($admin);
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

        /** @var \Illuminate\Auth\SessionGuard $admin_guard */
        $admin_guard = Auth::guard('admin');
        $admin_auth_key = $admin_guard->getName();

        $sessions = $admin
            ->sessions()
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->getTimestamp())
            ->orderBy('id')
            ->get()
            ->filter(function ($session) use ($admin_auth_key, $admin) {
                $payload = json_decode(base64_decode($session->payload), true);
                return isset($payload[$admin_auth_key]) && $payload[$admin_auth_key] == $admin->id;
            })
            ->map(function ($session) use ($sid) {
                $session->current = $session->id === $sid;
                $payload = json_decode(base64_decode($session->payload), true);
                $session->revoked = $payload['admin_revoked'] ?? false;
                return $session;
            })
            ->values();

        return SessionResource::collection($sessions);
    }

    /**
     * Revoke specified admin session.
     */
    public function revoke_admin_session(Request $request, Admin $admin, Session $session)
    {
        /** @var \Illuminate\Auth\SessionGuard $admin_guard */
        $admin_guard = Auth::guard('admin');
        $admin_auth_key = $admin_guard->getName();
        $payload = json_decode(base64_decode($session->payload), true);

        if (!isset($payload[$admin_auth_key]) || $payload[$admin_auth_key] != $admin->id) {
            return response()->json(
                [
                    'message' => 'Session does not belong to this admin.',
                ],
                404,
            );
        }

        $payload['admin_revoked'] = true;

        $session->payload = base64_encode(json_encode($payload));
        $session->save();

        return response()->noContent();
    }

    /**
     * Restore specified admin session.
     */
    public function restore_admin_session(Request $request, Admin $admin, Session $session)
    {
        /** @var \Illuminate\Auth\SessionGuard $admin_guard */
        $admin_guard = Auth::guard('admin');
        $admin_auth_key = $admin_guard->getName();
        $payload = json_decode(base64_decode($session->payload), true);

        if (!isset($payload[$admin_auth_key]) || $payload[$admin_auth_key] != $admin->id) {
            return response()->json(
                [
                    'message' => 'Session does not belong to this admin.',
                ],
                404,
            );
        }

        unset($payload['admin_revoked']);
        // $payload['admin_revoked'] = false;

        $session->payload = base64_encode(json_encode($payload));
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
            fn($query) => match ($request->input('status')) {
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
        if (!$registration->campaign()->first()->active()) {
            return response()->json(
                [
                    'message' => 'Registration campaign is expired or not active.',
                ],
                422,
            );
        }

        $now = now();
        $expiry = $now;
        $expiry->addMinutes(config('auth.admin_registration.expiry'));

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
