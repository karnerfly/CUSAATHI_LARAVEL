<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Dashboard\ChangeNameRequest;
use App\Http\Requests\Admin\Dashboard\ChangePasswordRequest;
use App\Http\Requests\Admin\Dashboard\UploadProfilePictureRequest;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Http\Resources\Session\SessionResource;
use App\Models\Admin;
use App\Models\Session;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Group('Admin Dashboard')]
class DashboardController extends Controller
{
    /**
     * Get details of current admin.
     */
    public function get_current_admin(Request $request)
    {
        return new AdminResource($request->user('admin'));
    }

    /**
     * Change current admin password.
     */
    public function change_password(ChangePasswordRequest $request)
    {
        $validated = $request->validated();
        $admin = $request->user('admin');

        $admin = Admin::find($admin->id);

        if (!Hash::check($validated['old_password'], $admin->password)) {
            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        $admin->password = Hash::make($validated['password']);
        $admin->save();

        Auth::guard('admin')->login($admin);

        return response()->noContent();
    }

    /**
     * Change current admin name.
     */
    public function change_name(ChangeNameRequest $request)
    {
        $name = $request->input('name');
        $admin = $request->user('admin');

        $admin = Admin::find($admin->id);

        $admin->name = $name;
        $admin->save();

        return response()->noContent();
    }

    /**
     * Upload profile picture.
     */
    public function upload_profile_picture(UploadProfilePictureRequest $request)
    {
        $file = $request->file('file');
        $admin = $request->user('admin');
        $admin = Admin::find($admin->id);

        $path = $file->store('profiles', 'public');

        $admin->profile_url = $path;
        $admin->save();

        return response()->noContent();
    }

    /**
     * Get active sessions.
     */
    public function get_sessions(Request $request)
    {
        $sid = $request->session()->getId();
        $admin = $request->user('admin');

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
     * Delete specified session.
     */
    public function delete_session(Request $request, Session $session)
    {
        $admin = $request->user('admin');

        /** @var \Illuminate\Auth\SessionGuard $admin_guard */
        $admin_guard = Auth::guard('admin');
        $admin_auth_key = $admin_guard->getName();
        $payload = json_decode(base64_decode($session->payload), true);

        if (!isset($payload[$admin_auth_key]) || $payload[$admin_auth_key] != $admin->id) {
            return response()->json(
                [
                    'message' => 'Invalid session.',
                ],
                404,
            );
        }

        $payload['admin_revoked'] = true;

        $session->payload = base64_encode(json_encode($payload));
        $session->save();

        return response()->noContent();
    }
}
