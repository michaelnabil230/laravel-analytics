<?php

namespace MichaelNabil230\LaravelAnalytics\Commands;

use Illuminate\Console\Command;
use MichaelNabil230\LaravelAnalytics\LaravelAnalyticQueries;

class GetTopCountPathsCommand extends Command
{
    public $signature = 'laravel-analytics:get-top-count-paths';

    public $description = 'Get all paths from the analytics';

    public function handle(): int
    {
        $urls = LaravelAnalyticQueries::topVisiterUrl(['url'])
            ->get()
            ->map(function ($visiter) {
                return [
                    'url' => $visiter->url,
                    'url_count' => $visiter->url_count,
                ];
            })
            ->toArray();

        $this->table(['Url', 'Count'], $urls);

        $this->newLine();
        $this->info('Total Urls is: ' . count($urls));

        return self::SUCCESS;
    }
}
