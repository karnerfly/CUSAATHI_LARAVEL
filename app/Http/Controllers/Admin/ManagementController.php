<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Http\Resources\Admin\SessionResource;
use App\Models\Admin;
use App\Models\Session;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;

#[Group('Admin Management')]
class ManagementController extends Controller
{
    /**
     * Get all admins
     */
    public function get_all_admins()
    {
        return Admin::withTrashed()->orderBy('id')->get();
    }

    /**
     * Store a newly created admin.
     */
    public function create_admin(StoreAdminRequest $request)
    {
        $admin = Admin::create($request->only(['name', 'email', 'password']));
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
     * Deactivate specified admin.
     */
    public function deactivate_admin(Admin $admin)
    {
        $admin->update(['active' => false]);

        return response()->noContent();
    }

    /**
     * Activate specified admin.
     */
    public function activate_admin(Admin $admin)
    {
        $admin->update(['active' => true]);

        return response()->noContent();
    }

    /**
     * Get specified admin sessions.
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
     * Soft Delete specified admin.
     */
    public function delete_admin(Admin $admin)
    {
        $admin->delete();
        $admin->sessions()->delete();

        return response()->noContent();
    }

    /**
     * Restore specified admin.
     */
    public function restore_admin(Admin $admin)
    {
        $admin->restore();

        return response()->noContent();
    }
}
