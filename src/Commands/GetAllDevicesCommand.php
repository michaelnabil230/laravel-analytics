<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\Models\Analytics;

class GetAllDevicesCommand extends Command
{
    public $signature = 'laravel-analytics:get-devices';

    public $description = 'Get all devices from the analytics table';

    public function handle(): int
    {
        $analytics = Analytics::query()
            ->except(['ajax', 'bots'])
            ->orderBy('visitors_count', 'DESC')
            ->groupBy('browser_family')
            ->get()
            ->map()
            ->toArray();

        $this->table(['Count', 'Kind', 'Model', 'Platform', 'Platform version', 'Is mobile'], $analytics);

        return self::SUCCESS;
    }
}
