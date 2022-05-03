<?php

namespace MichaelNabil230\LaravelAnalytics\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Cache\CacheManager;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MichaelNabil230\LaravelAnalytics\GeoIp\Driver as GeoIpDriver;

class GetGeoipData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected CacheManager|null $cache = null;

    protected bool $enableCache;

    protected int $cacheTtl;

    protected string $driverCache;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(protected Model $analytics)
    {
        $this->setCacheInstance();
    }

    public function setCacheInstance(): void
    {
        $this->enableCache = config('analytics.cache.enable', true);
        $this->driverCache = config('analytics.cache.driver', 'file');
        $this->cacheTtl = config('analytics.cache.ttl', 15);

        if ($this->enableCache) {
            $this->cache = $this->app['cache']->store($this->driverCache);
        }
    }

    public function handle()
    {
        if (app()->runningUnitTests()) {
            return;
        }

        $analytics = $this->analytics;
        $ip = $analytics->ip;

        if ($this->enableCache) {
            $data = $this->cache?->remember("get-data-by-{$ip}", $this->cacheTls, $this->getData($ip));
        } else {
            $data = $this->getData($ip);
        }

        $analytics->update($data);
    }

    private function getData($ip): array
    {
        /** @var GeoIpDriver $instance */
        $instance = app('geo-ip');

        $geoip = $instance->getDataFor($ip);

        return $geoip->getFormattedData();
    }
}
