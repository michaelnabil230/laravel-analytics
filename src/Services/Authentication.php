<?php

namespace MichaelNabil230\LaravelAnalytics\Services;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class Authentication
{
    public static function getUser(): Authenticatable|null
    {
        $guards = config('analytics.authentication.guards');
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            $auth = Auth::guard($guard);
            if ($auth->check()) {
                return $auth->user();
            }
        }

        return null;
    }
}
