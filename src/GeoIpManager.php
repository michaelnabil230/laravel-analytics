<?php

namespace MichaelNabil230\LaravelAnalytics;

use Illuminate\Support\Arr;
use Illuminate\Support\Manager;

class GeoIpManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('analytics.geo_ip.default', 'free_geo_ip');
    }

    public function registerStore(string $driver, array $params)
    {
        return $this->extend($driver, function () use ($params) {
            return $this->container->make($params['driver'], [
                'options' => Arr::get($params, 'options', []),
            ]);
        });
    }
}
