<?php

namespace MichaelNabil230\LaravelAnalytics\GeoIp;

use Illuminate\Support\Arr;

class IpStack extends Driver
{
    protected function getEndpoint($ip): string
    {
        $key = Arr::get($this->options, 'api_key', '');

        return "http://api.ipstack.com/{$ip}?access_key={$key}";
    }

    protected function latitude(): string
    {
        return $this->data->latitude;
    }

    protected function longitude(): string
    {
        return $this->data->longitude;
    }

    protected function country(): string
    {
        return $this->data->country_name;
    }

    protected function countryCode(): string
    {
        return $this->data->country_code;
    }

    protected function city(): string
    {
        return $this->data->city;
    }
}
