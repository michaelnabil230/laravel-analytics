<?php

namespace MichaelNabil230\LaravelAnalytics\GeoIp;

class FreeGeoIp extends Driver
{
    protected function getEndpoint($ip): string
    {
        return "https://freegeoip.live/json/{$ip}";
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
