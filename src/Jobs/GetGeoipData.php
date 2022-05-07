<?php

namespace MichaelNabil230\LaravelAnalytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MichaelNabil230\LaravelAnalytics\GeoIp\Driver as GeoIpDriver;
use MichaelNabil230\LaravelAnalytics\Models\Ip;

class GetGeoipData implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected Ip $ip)
    {
    }

    public function handle()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        /** @var GeoIpDriver $instance */
        $instance = app('geo-ip');

        $data = $instance
            ->getDataFor($this->ip->ip_address)
            ?->getFormattedData();

        $this->ip->update($data->toArray());
    }
}
