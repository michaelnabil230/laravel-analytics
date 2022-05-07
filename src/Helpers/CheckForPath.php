<?php

namespace MichaelNabil230\LaravelAnalytics\Helpers;

class CheckForPath
{
    public function __construct(protected string $host)
    {
    }

    public static function make(string $host): self
    {
        return new self($host);
    }

    public function getResult(): bool
    {
        return $this->hasMatchingPath();
    }

    private function hasMatchingPath(): bool
    {
        $paths = $this->getPathsByHost($this->host);

        foreach ($paths as $path) {
            if ($path !== '/') {
                $path = trim($path, '/');
            }

            if (request()->fullUrlIs($path) || request()->is($path)) {
                return true;
            }
        }

        return false;
    }

    private function getPathsByHost(string $host): array
    {
        $paths = config('analytics.ignore_paths', []);

        if (isset($paths[$host])) {
            return $paths[$host];
        }

        return array_filter($paths, function ($path) {
            return is_string($path);
        });
    }
}
