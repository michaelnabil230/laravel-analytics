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

    public function latitude(): string
    {
        return $this->data->latitude;
    }

    public function longitude(): string
    {
        return $this->data->longitude;
    }

    public function country(): string
    {
        return $this->data->country_name;
    }

    public function countryCode(): string
    {
        return $this->data->country_code;
    }

    public function city(): string
    {
        return $this->data->city;
    }
}
