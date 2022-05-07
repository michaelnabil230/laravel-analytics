<?php

namespace MichaelNabil230\LaravelAnalytics\Middleware;

use Closure;
use MichaelNabil230\LaravelAnalytics\Facades\LaravelAnalytics;

class Analytics
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null $typeRequest
     * @return mixed
     */
    public function handle($request, Closure $next, $typeRequest = null)
    {
        $visiter = LaravelAnalytics::recordVisiter($typeRequest ?: 'web-request');
        $request->merge([
            'visiter' => $visiter,
        ]);

        return $next($request);
    }
}
