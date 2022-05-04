<?php

namespace MichaelNabil230\LaravelAnalytics\Events\Contracts;

use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use Illuminate\Queue\SerializesModels;

abstract class SessionVisiterEvent
{
    use SerializesModels;

    public function __construct(public SessionVisiter $sessionVisiter)
    {
    }
}
