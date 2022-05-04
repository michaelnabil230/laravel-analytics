<?php

namespace MichaelNabil230\LaravelAnalytics\Services;

use Illuminate\Auth\Authenticatable;

class Authentication
{
    public static function getUser(): Authenticatable|null
    {
        $guards = config('analytics.authentication.guards', [null]);

        $auth = app('auth');
        foreach ($guards as $guard) {
            $auth = $auth->guard($guard);
            if ($auth->check()) {
                return $auth->user();
            }
        }

        return null;
    }
}
