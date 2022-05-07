<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\LaravelAnalyticQueries;

class GetIpCommand extends Command
{
    public $signature = 'laravel-analytics:get-ips';

    public $description = 'Get all geo ips from the analytics table';

    public function handle(): int
    {
        $ips = LaravelAnalyticQueries::ip()
            ->get()
            ->map(function ($ip) {
                return [
                    'ip_address' => $ip->ip_address,
                    'country' => $ip->country,
                    'country_code' => $ip->country_code,
                    'city' => $ip->city,
                    'latitude' => $ip->latitude,
                    'longitude' => $ip->longitude,
                ];
            })->toArray();

        $this->table(['Ip', 'Country', 'Country code', 'City', 'Latitude', 'Longitude'], $ips);

        $this->newLine();

        $this->info('Total Ips is: ' . count($ips));

        return self::SUCCESS;
    }
}
