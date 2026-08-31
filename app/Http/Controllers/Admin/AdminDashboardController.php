<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChangeNameReqest;
use App\Http\Requests\Admin\ChangePasswordReqest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminDashboardController extends Controller
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

    public function change_name(ChangeNameReqest $request)
    {
        $name = $request->input(['name']);
        $admin = $request->user('admin');

        $admin = Admin::find($admin->id);

        $admin->name = $name;
        $admin->save();

        return response()->noContent();
    }
}
