<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ForgotPasswordRequest;
use App\Http\Requests\Admin\LoginRequest;
use App\Http\Requests\Admin\ResetPasswordRequest;
use App\Http\Resources\Admin\AdminResource;
use App\Models\Admin;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AdminAuthController extends Controller
{
    /**
     * Login.
     */
    public function login(LoginRequest $request)
    {
        if (!Auth::guard('admin')->attempt($request->only(['email', 'password']))) {
            throw ValidationException::withMessages([
                'message' => 'invalid email or password.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'admin logged in successfully.',
            'admin' => new AdminResource($request->user('admin')),
        ]);
    }

    /**
     * Request for password reset link.
     */
    public function forgot_password(ForgotPasswordRequest $request)
    {
        $status = Password::broker('admins')->sendResetLink($request->only('email'));

        if ($status !== Password::ResetLinkSent) {
            throw ValidationException::withMessages([
                'email' => __($status),
            ]);
        }

        return response()->json([
            'status' => __($status),
        ]);
    }

    /**
     *  Reset password via token.
     */
    public function reset_password(ResetPasswordRequest $request)
    {
        $status = Password::broker('admins')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Admin $admin, string $password) {
                $admin->forceFill([
                    'password' => Hash::make($password),
                ]);

                $admin->save();

                event(new PasswordReset($admin));
            },
        );

        if ($status !== Password::PasswordReset) {
            return response()->json(
                [
                    'message' => __($status),
                ],
                422,
            );
        }

        return response()->json([
            'message' => __($status),
        ]);
    }

    /**
     * Logout.
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return response(status: 204);
    }
}
