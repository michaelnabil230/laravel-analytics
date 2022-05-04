<?php

namespace VendorName\Skeleton\Database\Factories;

use MichaelNabil230\LaravelAnalytics\Models\Ip;
use Illuminate\Database\Eloquent\Factories\Factory;

class IpFactory extends Factory
{
    protected $model = Ip::class;

    public function definition()
    {
        return [
            'ip' => $this->faker->ipv4,
            'country' => $this->faker->country,
            'country_code' => $this->faker->countryCode,
            'city' => $this->faker->city,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
