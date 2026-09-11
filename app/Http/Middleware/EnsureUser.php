<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUser
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web')->check()) {
            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        $user = Auth::guard('web')->user();

        if (!$user->active) {
            return response()->json(
                [
                    'message' => 'Your account has been deactivated. Please contact support.',
                ],
                403,
            );
        }

        Auth::shouldUse('web');

        return $next($request);
    }
}
