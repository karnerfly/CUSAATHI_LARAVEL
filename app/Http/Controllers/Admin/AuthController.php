<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Auth\AdminRegistrationCompleteRequest;
use App\Http\Requests\Admin\Auth\AdminRegistrationRequest;
use App\Http\Requests\Admin\Auth\ForgotPasswordRequest;
use App\Http\Requests\Admin\Auth\LoginRequest;
use App\Http\Requests\Admin\Auth\ResetPasswordRequest;
use App\Http\Resources\Admin\Admin\AdminResource;
use App\Models\Admin;
use App\Models\AdminRegistration;
use App\Models\AdminRegistrationCampaign;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

#[Group('Admin Auth')]
class AuthController extends Controller
{
    /**
     * Login.
     */
    public function login(LoginRequest $request)
    {
        $auth_passed = Auth::guard('admin')->attempt(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'active' => true,
            ],
            $request->input('remember', false),
        );

        if (!$auth_passed) {
            return response()->json(
                [
                    'message' => 'Invalid email or password.',
                ],
                401,
            );
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Admin logged in successfully.',
            'admin' => new AdminResource($request->user('admin')),
        ]);
    }

    /**
     * Request for password reset link.
     */
    public function forgot_password(ForgotPasswordRequest $request)
    {
        $status = Password::broker('admins')->sendResetLink([
            'email' => $request->input('email'),
            'active' => true,
        ]);

        if ($status !== Password::ResetLinkSent) {
            Log::error('failed to send mail', [
                'email' => $request->input('email'),
                'error' => __($status),
            ]);
        }

        return response()->noContent();
    }

    /**
     *  Reset password via token.
     */
    public function reset_password(ResetPasswordRequest $request)
    {
        $status = Password::broker('admins')->reset($request->validated(), function (Admin $admin, string $password) {
            $admin->forceFill([
                'password' => Hash::make($password),
            ]);

            $admin->save();

            event(new PasswordReset($admin));
        });

        if ($status !== Password::PasswordReset) {
            return response()->json(
                [
                    'message' => __($status),
                ],
                422,
            );
        }

        return response()->noContent();
    }

    /**
     *  Request for an admin registration.
     */
    #[QueryParameter('cmpid', required: true, type: 'integer')]
    #[QueryParameter('expires', required: true, type: 'integer')]
    #[QueryParameter('signature', required: true, type: 'string')]
    public function registration_request(AdminRegistrationRequest $request)
    {
        $cmpid = $request->query('cmpid');

        if (!AdminRegistrationCampaign::findOrFail($cmpid)->active()) {
            return response()->json(
                [
                    'message' => 'Invalid campaign.',
                ],
                422,
            );
        }

        $validated = $request->validated();
        $token = Str::random(48);

        AdminRegistration::create([
            'email' => $validated['email'],
            'admin_registration_campaign_id' => $cmpid,
            'token' => $token,
            'payload' => $validated,
        ]);

        return response()->noContent();
    }

    /**
     *  Display the status of an admin registration campaign.
     */
    #[QueryParameter('cmpid', required: true, type: 'integer')]
    public function registration_status(Request $request)
    {
        $cmpid = $request->query('cmpid');

        $campaign = AdminRegistrationCampaign::find($cmpid);

        return response()->json(
            [
                'campaign' => $campaign ? $campaign->only(['id', 'active', 'expires_at']) : null,
            ],
            200,
        );
    }

    /**
     *  Complete the request for an admin registration.
     */
    public function complete_registration_request(AdminRegistrationCompleteRequest $request)
    {
        $validated = $request->validated();

        $registration = AdminRegistration::where('token', $validated['token'])
            ->where('expiration', '>', now()->timestamp)
            ->first();

        if (!$registration || !$registration->campaign()->first()->active()) {
            abort(403, 'This action is unauthorized.');
        }

        $details = $registration->details;

        if (Admin::where('email', $details['email'])->exists()) {
            return response()->json(
                [
                    'message' => 'Admin already exists.',
                ],
                409,
            );
        }

        $admin = Admin::create([
            'name' => $details['name'],
            'email' => $details['email'],
            'password' => $validated['password'],
        ]);

        $admin->refresh();
        $registration->delete();

        return response()->json(
            [
                'message' => 'Admin created.',
                'admin' => AdminResource::make($admin),
            ],
            201,
        );
    }

    /**
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return response(status: 204);
    }
}
