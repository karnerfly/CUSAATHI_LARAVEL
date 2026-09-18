<?php

namespace App\Auditing\Resolvers;

use Illuminate\Support\Facades\Auth;
use OwenIt\Auditing\Contracts\UserResolver;

class CustomActorResolver implements UserResolver
{
    /**
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public static function resolve()
    {
        if (request()->is('api/v2/admins/*')) {
            return Auth::guard('admin')->user();
        }

        if (request()->is('api/v2/users/*')) {
            return Auth::guard('web')->user();
        }

        return null;
    }
}
