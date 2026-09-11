<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Dashboard\ChangeNameRequest;
use App\Http\Requests\User\Dashboard\ChangePasswordRequest;
use App\Http\Requests\User\Dashboard\UploadProfilePictureRequest;
use App\Http\Resources\Session\SessionResource;
use App\Http\Resources\User\UserResource;
use App\Models\Session;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

#[Group('User Dashboard')]
class DashboardController extends Controller
{
    /**
     * Get details of current user.
     */
    public function get_current_user(Request $request)
    {
        return new UserResource($request->user('web'));
    }

    /**
     * Change current user password.
     */
    public function change_password(ChangePasswordRequest $request)
    {
        $validated = $request->validated();
        $user = $request->user('web');

        $user = User::find($user->id);

        if (!Hash::check($validated['old_password'], $user->password)) {
            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        Auth::guard('web')->login($user);

        return response()->noContent();
    }

    /**
     * Change current user name.
     */
    public function change_name(ChangeNameRequest $request)
    {
        $name = $request->input('name');
        $user = $request->user('web');

        $user = User::find($user->id);

        $user->name = $name;
        $user->save();

        return response()->noContent();
    }

    /**
     * Upload profile picture.
     */
    public function upload_profile_picture(UploadProfilePictureRequest $request)
    {
        $file = $request->file('file');
        $user = $request->user('web');
        $user = User::find($user->id);

        $path = $file->store('profiles', 'public');

        $user->profile_url = $path;
        $user->save();

        return response()->noContent();
    }

    /**
     * Get active sessions.
     */
    public function get_sessions(Request $request)
    {
        $sid = $request->session()->getId();
        $user = $request->user('web');
        $sessions = $user
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
     * Delete specified session.
     */
    public function delete_session(Request $request, Session $session)
    {
        $user = $request->user('web');
        $session = $user->sessions()->where('id', $session->id)->firstOrFail();
        $session->delete();

        return response()->noContent();
    }
}
