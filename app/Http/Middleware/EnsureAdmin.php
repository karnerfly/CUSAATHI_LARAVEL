<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        $admin = Auth::guard('admin')->user();

        if (!$admin->active) {
            return response()->json(
                [
                    'message' => 'Your account has been deactivated. Please contact support.',
                ],
                403,
            );
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
