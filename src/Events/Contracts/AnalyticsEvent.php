<?php

namespace MichaelNabil230\LaravelAnalytics\Events\Contracts;

use MichaelNabil230\LaravelAnalytics\Models\Analytics;
use Illuminate\Queue\SerializesModels;

abstract class AnalyticsEvent
{
    use SerializesModels;

    public function __construct(public Analytics $analytics)
    {
    }
}
