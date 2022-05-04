<?php

namespace VendorName\Skeleton\Database\Factories;

use Illuminate\Support\Carbon;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionVisiterFactory extends Factory
{
    protected $model = SessionVisiter::class;

    public function definition()
    {
        return [
            'ip_id' => $this->ip_id,
            'start_at' => Carbon::now(),
            'end_at' => Carbon::now()->subDays($this->faker->numberBetween(0, 50))
        ];
    }
}
