<?php

namespace MichaelNabil230\LaravelAnalytics;

use Spatie\LaravelPackageTools\Package;
use MichaelNabil230\LaravelAnalytics\GeoIpManager;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MichaelNabil230\LaravelAnalytics\Commands\GetGeoIpCommand;
use MichaelNabil230\LaravelAnalytics\Commands\GetPathsCommand;
use MichaelNabil230\LaravelAnalytics\Commands\GetAllDevicesCommand;

class LaravelAnalyticsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-analytics')
            ->hasConfigFile()
            ->hasMigrations([
                'create_laravel-ips_table',
                'create_laravel-session_visiters_table',
                'create_laravel-visiters_table',
            ])
            ->hasCommands([
                GetAllDevicesCommand::class,
                GetGeoIpCommand::class,
                GetPathsCommand::class,
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
}
