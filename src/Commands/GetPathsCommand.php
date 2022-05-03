<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\Models\Analytics;

class GetPathsCommand extends Command
{
    public $signature = 'laravel-analytics:get-paths';

    public $description = 'Get all paths from the analytics table';

    public function handle(): int
    {
        $analytics = Analytics::query()
            ->except(['ajax', 'bots'])
            ->orderBy('visitors_count', 'DESC')
            ->groupBy('url')
            ->get()
            ->map()
            ->toArray();

        $this->table(['Count', 'Path'], $analytics);

        return self::SUCCESS;
    }
}
