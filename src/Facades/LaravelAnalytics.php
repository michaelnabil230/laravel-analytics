<?php

namespace MichaelNabil230\LaravelAnalytics\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MichaelNabil230\LaravelAnalytics\Models\Visiter recordVisit($agent = null, string $event = '')
 *
 * @see \MichaelNabil230\LaravelAnalytics\LaravelAnalytics
 */
class LaravelAnalytics extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-analytics';
    }
}
