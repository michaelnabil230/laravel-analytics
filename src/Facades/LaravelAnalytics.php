<?php

namespace MichaelNabil230\LaravelAnalytics\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \MichaelNabil230\LaravelAnalytics\Models\Visiter recordVisiter(string $typeRequest, $agent = null)
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
