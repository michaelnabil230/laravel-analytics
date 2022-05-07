<?php

namespace MichaelNabil230\LaravelAnalytics\GeoIp;

use Illuminate\Support\Collection;

class FreeGeoIp extends Driver
{
    protected function getEndpoint($ip): string
    {
        return "https://ipwho.is/{$ip}?fields=latitude,longitude,country,country_code,city,flag";
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
        return $this->data->country;
    }

    protected function countryCode(): string
    {
        return $this->data->country_code;
    }

    protected function city(): string
    {
        return $this->data->city;
    }

    public function additionalData(): Collection
    {
        return collect([
            'flag' => $this->data->flag,
        ]);
    }
}
