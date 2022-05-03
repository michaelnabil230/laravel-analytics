<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\Models\Analytics;

class GetGeoIpCommand extends Command
{
    public $signature = 'laravel-analytics:get-geoip';

    public $description = 'Get all geo ips from the analytics table';

    public function handle(): int
    {
        $analytics = Analytics::query()
            ->except(['ajax', 'bots'])
            ->orderBy('visitors_count', 'DESC')
            ->groupBy('ip')
            ->map()
            ->toArray();

        $this->table(['Latitude', 'Longitude', 'Country code', 'Country code3', 'Country name', 'Region', 'City', 'Postal code', 'Area code', 'Dma code', 'Metro code', 'Continent code'], $analytics);

        return self::SUCCESS;
    }
}
