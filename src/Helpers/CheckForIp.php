<?php

namespace MichaelNabil230\LaravelAnalytics\Helpers;

use Symfony\Component\HttpFoundation\IpUtils;

class CheckForIp
{
    protected $ips;

    public function __construct(protected string $ip)
    {
        $this->ips = config('analytics.do_not_track_ips', []);
    }

    public static function make(string $ip): self
    {
        return new self($ip);
    }

    public function getResult(): bool
    {
        return $this->inListIgnore() || $this->checkIsIp();
    }

    private function checkIsIp(): bool
    {
        return IpUtils::checkIp($this->ip, $this->ips);
    }

    private function inListIgnore(): bool
    {
        return in_array($this->ip, $this->ips);
    }
}
