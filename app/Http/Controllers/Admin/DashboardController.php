<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeNameReqest;
use App\Http\Requests\Admin\ChangePasswordReqest;
use App\Http\Requests\Admin\UploadProfilePictureReqest;
use App\Http\Resources\Admin\AdminResource;
use App\Http\Resources\SessionResource;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

#[Group('Admin Dashboard')]
class DashboardController extends Controller
{
    /**
     * Get details of current admin
     */
    public function get_current_admin(Request $request)
    {
        return new AdminResource($request->user('admin'));
    }

    /**
     * Change current admin password
     */
    public function change_password(ChangePasswordReqest $request)
    {
        $validated = $request->only(['old_password', 'password', 'password_confirmation']);
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

        return response()->noContent();
    }

    /**
     * Change current admin name
     */
    public function change_name(ChangeNameReqest $request)
    {
        $name = $request->input(['name']);
        $admin = $request->user('admin');

        $admin = Admin::find($admin->id);

        $admin->name = $name;
        $admin->save();

        return response()->noContent();
    }

    /**
     * Upload profile picture
     */
    public function upload_profile_picture(UploadProfilePictureReqest $request)
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
     * Get active sessions
     */
    public function get_sessions(Request $request)
    {
        $sid = $request->session()->getId();
        $admin = $request->user('admin');
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
}
