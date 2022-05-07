<?php

namespace MichaelNabil230\LaravelAnalytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MichaelNabil230\LaravelAnalytics\GeoIp\Driver as GeoIpDriver;
use MichaelNabil230\LaravelAnalytics\Models\Ip;

class GetGeoipData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Ip $ip)
    {
        //
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
