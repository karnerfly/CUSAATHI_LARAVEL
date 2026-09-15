<?php

namespace App\Http\Controllers\User;

use App\Enums\AuthProvider;
use App\Enums\SocialAuthProvider;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\Auth\CompleteProfileRequest;
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
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Laravel\Socialite\Socialite;
use RuntimeException;

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
        $auth_passed = Auth::guard('web')->attempt(
            [
                'email' => $request->input('email'),
                'password' => $request->input('password'),
                'provider' => AuthProvider::LOCAL,
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
            'message' => 'User logged in successfully.',
            'user' => new UserResource($request->user('web')),
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
    public function social_login_redirect(Request $request, SocialAuthProvider $provider)
    {
        $redirect_key = 'social_ ' . $provider->value . '_redirect';
        $redirect = $request->query('redirect', config('app.client_url'));
        if (!str_starts_with($redirect, config('app.client_url'))) {
            $redirect = config('app.client_url');
        }

        // $request->session()->put($redirect_key, $redirect);

        $state = Crypt::encrypt(
            json_encode([
                $redirect_key => $redirect,
                'expires_at' => now()->addMinute()->timestamp,
            ]),
        );

        /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
        $socialite = Socialite::driver($provider);

        return response()->json(
            [
                'url' => $socialite
                    ->stateless()
                    ->scopes(['profile', 'email'])
                    ->with([
                        'prompt' => 'select_account',
                        'state' => $state,
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
    public function social_login_callback(Request $request, SocialAuthProvider $provider)
    {
        try {
            $redirect_key = 'social_ ' . $provider->value . '_redirect';

            $state_q = $request->query('state');
            if (!$state_q) {
                throw new RuntimeException('invalid authentication parameters');
            }

            $state = json_decode(Crypt::decrypt($state_q), true);
            if (!is_array($state) || !isset($state[$redirect_key], $state['expires_at'])) {
                throw new RuntimeException('invalid authentication state');
            }

            if (now()->timestamp > $state['expires_at']) {
                throw new RuntimeException('authentication state expired');
            }

            $redirect = $state[$redirect_key];

            /** @var \Laravel\Socialite\Two\AbstractProvider $socialite */
            $socialite = Socialite::driver($provider);
            $social_user = $socialite->stateless()->user();

            $user = User::where(['provider' => $provider->value, 'provider_id' => $social_user->getId()])->first();

            if (!$user) {
                $other_account = User::where(['email' => $social_user->getEmail()])->exists();
                if ($other_account) {
                    throw new RuntimeException('another authentication method was used with this email');
                }

                $user = User::create([
                    'name' => $social_user->getName(),
                    'email' => $social_user->getEmail(),
                    'email_verified_at' => now(),
                    'profile_url' => $social_user->getAvatar(),
                    'provider' => $provider->value,
                    'provider_id' => $social_user->getId(),
                ]);
            }

            if (!$user->isCompleted()) {
                $redirect =
                    rtrim(config('app.client_url'), '/') . '/registration/onboarding?redirect=' . urlencode($redirect);
            }

            Auth::guard('web')->login($user, true);

            $request->session()->regenerate();

            return redirect($redirect);
        } catch (\Throwable $th) {
            $message = $th->getMessage();
            $redirect =
                rtrim(config('app.client_url'), '/') .
                '/auth/login/sso/error?error=' .
                urlencode($message) .
                '&provider=' .
                urlencode($provider->value);

            return redirect($redirect);
        }
    }

    /**
     * Complete profile.
     */
    public function complete_profile(CompleteProfileRequest $request)
    {
        $user = $request->user('web');

        if ($user->isCompleted()) {
            return response()->json(
                [
                    'message' => 'User profile already completed.',
                ],
                422,
            );
        }

        $validated = $request->validated();
        $user->update($validated);

        return response()->noContent();
    }
}
