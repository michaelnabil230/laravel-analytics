<?php

namespace MichaelNabil230\LaravelAnalytics\Traits;

use MichaelNabil230\LaravelAnalytics\Models\Analytics;

trait Authentication
{
    public function analytics()
    {
        return $this->morphMany(Analytics::class, 'authenticatable')->latest('created_at');
    }

    public function latestAnalytics()
    {
        return $this->morphOne(Analytics::class, 'authenticatable')->latestOfMany('created_at');
    }
}
