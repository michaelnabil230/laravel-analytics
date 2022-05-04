<?php

namespace MichaelNabil230\LaravelAnalytics\GeoIp;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

abstract class Driver
{
    protected Object $data;

    public function __construct(
        protected array $options = [],
    ) {
    }

    public function getDataFor(string $ip)
    {
        $url = $this->getEndpoint($ip);
        $headers = Arr::get($this->options, 'headers', []);

        $response = Http::withHeaders($headers)->get($url);

        if ($response->ok()) {
            $this->data = $response->object();

            return $this;
        }

        return null;
    }

    public function getFormattedData(): array
    {
        return [
            'latitude' => $this->latitude() ?: null,
            'longitude' => $this->longitude() ?: null,
            'country' => $this->country() ?: '',
            'country_code' => $this->countryCode() ?: '',
            'city' => $this->city() ?: '',
        ];
    }

    abstract protected function getEndpoint(string $ip): string;

    abstract protected function latitude(): string;

    abstract protected function longitude(): string;

    abstract protected function country(): string;

    abstract protected function countryCode(): string;

    abstract protected function city(): string;
}
