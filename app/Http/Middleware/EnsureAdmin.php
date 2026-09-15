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

        if ($request->session()->get('admin_revoked', false) === true || !$admin->active) {
            // Auth::guard('admin')->logout();

            // $request->session()->forget('admin_revoked');

            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        Auth::shouldUse('admin');

        return $next($request);
    }
}
