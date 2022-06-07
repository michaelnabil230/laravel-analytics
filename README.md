
[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/support-ukraine.svg?t=1" />](https://supportukrainenow.org)

# This is my package laravel-analytics

[![Latest Version on Packagist](https://img.shields.io/packagist/v/michaelnabil230/laravel-analytics.svg?style=flat-square)](https://packagist.org/packages/michaelnabil230/laravel-analytics)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/michaelnabil230/laravel-analytics/run-tests?label=tests)](https://github.com/michaelnabil230/laravel-analytics/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/michaelnabil230/laravel-analytics/Check%20&%20fix%20styling?label=code%20style)](https://github.com/michaelnabil230/laravel-analytics/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/michaelnabil230/laravel-analytics.svg?style=flat-square)](https://packagist.org/packages/michaelnabil230/laravel-analytics)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.


## Installation

You can install the package via composer:

```bash
composer require michaelnabil230/laravel-analytics
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="laravel-analytics-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-analytics-config"
```

This is the contents of the published config file:

```php
return [

    'ip_model' => Ip::class,
    'session_visiter_model' => SessionVisiter::class,
    'visiter_model' => Visiter::class,

    /*
     * Which route paths are not trackable?
     */
    'ignore_paths' => [
        // 'api/*',
    ],

    /*
     * The Do Not Track Ips is used to disable Analytics for some IP addresses:
     *
     *     '127.0.0.1', '192.168.1.1'
     *
     * You can set ranges of IPs
     *     '192.168.0.1-192.168.0.100'
     *
     * And use net masks
     *      127.0.0.0/24 -> range  127.0.0.1 - 127.0.0.255
     *     '172.17.0.0/255.255.0.0'
     */
    'do_not_track_ips' => [
        '127.0.0.2',
    ],

    /*
    |--------------------------------------------------------------------------
    | Drivers GeoIp
    |--------------------------------------------------------------------------
    |
    | Should the geoip data be collected?
    |
    | Set the geoip driver.
    |
    | Supported: "free_geo_ip", "ip_stack"
    */
    'geo_ip' => [
        'enabled' => true,
        'default' => 'free_geo_ip',
        'drivers' => [
            'ip_stack' => [
                'driver' => MichaelNabil230\LaravelAnalytics\GeoIp\IpStack::class,
                'options' => [
                    'api_key' => '',
                    'headers' => [
                        // 'X-First' => 'foo',
                    ],
                ],
            ],
            'free_geo_ip' => [
                'driver' => MichaelNabil230\LaravelAnalytics\GeoIp\FreeGeoIp::class,
                'options' => [
                    'headers' => [
                        // 'X-First' => 'foo',
                    ],
                ],
            ],
        ],
    ],

    /*
     * All bot if you want defected one.
     */
    'bot_browsers' => [
        'curl',
        'python-requests',
        'python-urllib',
        'wget',
        'unk',
        'perl',
        'go-http-client',
    ],

    /*
     * Laravel internal variables on user authentication and login.
     */
    'authentication' => [
        'guards' => [], // defaults to ['web']
    ],
];
```

### Register Middleware

You may register the package middleware in the `app/Http/Kernel.php` file:

```php
<?php 

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel {
    /**
    * The application's route middleware.
    *
    * @var array
    */
    protected $routeMiddleware = [
        /**** OTHER MIDDLEWARE ****/
        'analytics' => \MichaelNabil230\LaravelAnalytics\Middleware\Analytics::class,
    ];
}
```


You can add the page view middleware to a specific route group, e.g. `web.php` like so:


```php
Route::middleware('analytics')->group(function () {
    // ...
});
```

Then you can pass `typeRequest` in your routes and default is `web-request` using middleware: 

```php
Route::middleware('analytics:api-request')->group(function () {
    // ...
});
```

## Usage

```php

use Illuminate\Support\Carbon;
use MichaelNabil230\LaravelAnalytics\LaravelAnalyticQueries;

$topEvents = LaravelAnalyticQueries::topVisiterEvent()
        // ->where('event', 'test event')
        // ->is('bots')
        // ->is('ajax', false)
        // ->uniqueSession()
        // ->uniqueIp()
        // ->with(['data'])
        // ->take(10)
        // ->select('id') // Is very important: Don't use `select` here pass what u need in `topVisiterEvent`
        // ->addSelect([DB::raw("COUNT(*) as 'count'")])
        ->period(Carbon::now(), Carbon::now()->addDays(7))
        ->limit(10)
        ->get();
```

## Testing

```bash
composer test
```

## Support
<p>
    <a href="https://www.buymeacoffee.com/michaelnabil230"> 
         <img align="left" src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="50" width="210" alt="michaelnabil230" />
    </a>
    <a href="https://ko-fi.com/michaelnabil230"> 
        <img align="left" src="https://cdn.ko-fi.com/cdn/kofi3.png?v=3" height="50" width="210" alt="michaelnabil230" />
    </a>
</p>
<br><br>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Michael Nabil](https://github.com/michaelnabil230)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
