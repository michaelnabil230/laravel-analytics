<?php

namespace MichaelNabil230\LaravelAnalytics\Middleware;

use Closure;
use MichaelNabil230\LaravelAnalytics\LaravelAnalytics;

class RecordVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        LaravelAnalytics::recordVisit();

        return $next($request);
    }
}
