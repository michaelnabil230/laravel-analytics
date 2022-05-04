<?php

namespace MichaelNabil230\LaravelAnalytics\Observers;

use Illuminate\Support\Facades\Cache;
use MichaelNabil230\LaravelAnalytics\Models\Ip;
use MichaelNabil230\LaravelAnalytics\Jobs\GetGeoipData;

class IpObserver
{
    /**
     * Handle the Ip "created" event.
     *
     * @param  Ip  $ip
     * @return void
     */
    public function created(Ip $ip)
    {
        GetGeoipData::dispatch($ip);
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  Ip  $ip
     * @return void
     */
    public function deleted(Ip $ip)
    {
        Cache::forget('data-ip-' . $ip->ip_address);
    }
}
