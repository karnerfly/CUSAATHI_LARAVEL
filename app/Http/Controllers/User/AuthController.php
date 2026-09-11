<?php

namespace App\Http\Controllers\User;

use App\Enums\AuthProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\ForgotPasswordRequest;
use App\Http\Requests\User\Auth\LoginRequest;
use App\Http\Requests\User\Auth\ResetPasswordRequest;
use App\Http\Requests\User\Auth\StoreUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Socialite;

#[Group('User Auth')]
class AuthController extends Controller
{
    /**
     * Register.
     */
    public function register(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::create([...$validated, 'provider' => AuthProvider::LOCAL]);

        event(new Registered($user));

        return response()->json(
            [
                'message' => 'Registration successful. Please verify your email.',
            ],
            201,
        );
    }

    /**
     * Verify email.
     */
    #[QueryParameter('expires', required: true, type: 'integer')]
    #[QueryParameter('signature', required: true, type: 'string')]
    public function verify_email(Request $request, int $id, string $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json(['message' => 'Email has been successfully verified'], 200);
    }

    /**
     * Resend verification email.
     */
    public function resend_email_verification(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(
                [
                    'message' => 'Email already verified.',
                ],
                200,
            );
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(
            [
                'message' => 'A new verification link has been sent to your email address.',
            ],
            200,
        );
    }

    /**
     * Login.
     */
    public function login(LoginRequest $request)
    {
        $auth_passed = Auth::guard()->attempt([
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'provider' => AuthProvider::LOCAL,
            'active' => true,
        ]);

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
            'message' => 'User logged in successfully.',
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Request for password reset link.
     */
    public function forgot_password(ForgotPasswordRequest $request)
    {
        $status = Password::broker('users')->sendResetLink([
            'email' => $request->input('email'),
            'provider' => AuthProvider::LOCAL,
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
        $status = Password::broker('users')->reset($request->validated(), function (User $user, string $password) {
            $user->forceFill([
                'password' => Hash::make($password),
            ]);

            $user->save();

            event(new PasswordReset($user));
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
     * Logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return response(status: 204);
    }

    /**
     * Social login redirect.
     */
    public function social_login_redirect(Request $request, AuthProvider $provider)
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);

        return response()->json(
            [
                'url' => $socialite
                    ->stateless()
                    ->scopes(['profile', 'email'])
                    ->with([
                        'prompt' => 'select_account',
                    ])
                    ->redirect()
                    ->getTargetUrl(),
            ],
            200,
        );
    }

    /**
     * Social login callback.
     */
    public function social_login_callback(Request $request, AuthProvider $provider)
    {
        /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);
        $socialite->stateless()->user();

        return redirect(config('app.client_url'));
    }
}
