<?php

use MichaelNabil230\LaravelAnalytics\Models\Ip;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use MichaelNabil230\LaravelAnalytics\Models\Visiter;

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
