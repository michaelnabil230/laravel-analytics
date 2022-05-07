<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\LaravelAnalyticQueries;

class GetTopCountDevicesCommand extends Command
{
    public $signature = 'laravel-analytics:get-count-top-devices';

    public $description = 'Get all devices from the analytics';

    public function handle(): int
    {
        $devices = LaravelAnalyticQueries::topVisiterBrowser(['browser'])
            ->is('bot', false)
            ->is('ajax', false)
            ->get()
            ->map(function ($visiter) {
                return [
                    'browser' => $visiter->browser,
                    'browser_count' => $visiter->browser_count,
                ];
            })
            ->toArray();

        $this->table(['Browser', 'Browser count'], $devices);

        $this->newLine();
        $this->info('Total Devices is: ' . count($devices));

        return self::SUCCESS;
    }
}
