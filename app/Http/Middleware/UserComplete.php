<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserComplete
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user('web')->isCompleted()) {
            return $next($request);
        }

        return response()->json(
            [
                'message' => 'Incomplete user profile.',
            ],
            403,
        );
    }
}
