<?php

namespace App\Http\Middleware;

use App\Models\Session;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionRevoked
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sid = $request->session()->getId();
        $session = Session::find($sid);

        if (!$session || $session->revoked) {
            return response()->json(
                [
                    'message' => 'Unauthenticated.',
                ],
                401,
            );
        }

        return $next($request);
    }
}
