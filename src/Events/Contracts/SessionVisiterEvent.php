<?php

namespace MichaelNabil230\LaravelAnalytics\Events\Contracts;

use Illuminate\Queue\SerializesModels;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;

abstract class SessionVisiterEvent
{
    use SerializesModels;

    public function __construct(public SessionVisiter $sessionVisiter)
    {
    }
}
