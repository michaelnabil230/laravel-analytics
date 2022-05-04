<?php

namespace VendorName\Skeleton\Database\Factories;

use Illuminate\Support\Carbon;
use MichaelNabil230\LaravelAnalytics\Models\Analytics;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalyticsFactory extends Factory
{
    protected $model = Analytics::class;

    public function definition()
    {
        $botAgents = [
            'curl/7.17.1 (mips-unknown-linux-gnu) libcurl/7.17.1 OpenSSL/0.9.8i zlib/1.2.3',
            'python-requests/2.18.4',
            'Python-urllib/2.7',
            'Wget(linux)',
            '',
        ];

        return [
            'ip' => $this->faker->ipv4,
            'event' => ['page-name', 'page-view-product', 'page-view-category', 'page-view-cart', 'page-view-checkout', 'make-order'][mt_rand(0, 5)],
            'method' => $this->faker->randomElement(['GET', 'POST', 'PUT', 'DELETE']),
            'url' => $this->faker->url,
            'referer' => $this->faker->url,
            'user_agent' => $this->faker->userAgent,
            'is' => [
                'ajax' => $this->faker->boolean,
                'bot' => $this->faker->boolean,
                'desktop' => $this->faker->boolean,
                'mobile' => $this->faker->boolean,
                'touch' => $this->faker->boolean,
            ],
            'bot' => $botAgents[mt_rand(0, 4)],
            'os_family' => ['Windows', 'Linux'][mt_rand(0, 1)],
            'os' => ['Windows 10.0', 'Ubuntu'][mt_rand(0, 1)],
            'browser_family' => ['Chrome', 'Firefox'][mt_rand(0, 1)],
            'browser' => ['Chrome 67.0', 'Firefox 58.0'][mt_rand(0, 1)],
            'browser_language_family' => ['ru', 'en', 'tl'][mt_rand(0, 2)],
            'browser_language' => $this->faker->languageCode,
            'country' => $this->faker->country,
            'country_code' => $this->faker->countryCode,
            'city' => $this->faker->city,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'created_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 50))
        ];
    }
}
