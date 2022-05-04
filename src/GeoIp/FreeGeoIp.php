<?php

namespace MichaelNabil230\LaravelAnalytics\GeoIp;

class FreeGeoIp extends Driver
{
    protected function getEndpoint($ip): string
    {
        return "https://freegeoip.live/json/{$ip}";
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
