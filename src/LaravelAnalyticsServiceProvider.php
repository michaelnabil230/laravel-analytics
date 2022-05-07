<?php

namespace MichaelNabil230\LaravelAnalytics;

use Illuminate\Http\Request;
use MichaelNabil230\LaravelAnalytics\Commands\GetIpCommand;
use MichaelNabil230\LaravelAnalytics\Commands\GetTopCountDevicesCommand;
use MichaelNabil230\LaravelAnalytics\Commands\GetTopCountPathsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelAnalyticsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-analytics')
            ->hasConfigFile()
            ->hasMigrations('create_laravel-analytics_table')
            ->hasCommands([
                GetTopCountDevicesCommand::class,
                GetIpCommand::class,
                GetTopCountPathsCommand::class,
            ]);
    }

    public function packageRegistered()
    {
        $this->app->bind('laravel-analytics', function () {
            return new LaravelAnalytics();
        });

        $this->app->extend(GeoIpManager::class, function (GeoIpManager $manager, $app) {
            foreach ($app['config']->get('analytics.geo_ip.drivers', []) as $driver => $params) {
                $manager->registerStore($driver, $params);
            }

            return $manager;
        });

        $this->app->bind('geo-ip', function ($app) {
            return $app->make(GeoIpManager::class)->driver();
        });
    }

    public function bootingPackage()
    {
        Request::macro('visiter', function ($key = null) {
            return collect($this->visiter)->when($key, function ($collection, $key) {
                return $collection->get($key);
            });
        });
    }
}
