<?php

namespace MichaelNabil230\LaravelAnalytics;

use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use MichaelNabil230\LaravelAnalytics\Models\Ip;

class LaravelAnalyticQueries
{
    public function __construct(protected Model $model)
    {
    }

    public static function instance(Model $model): self
    {
        return new static($model);
    }

    public function visiter()
    {
        return $this->model;
    }

    public function ip()
    {
        return new Ip();
    }

    public function visiterTop($parameters)
    {
        return $this->visiter()->except(['ajax', 'bots'])->top($parameters);
    }

    public function ipTop($parameters)
    {
        return $this->ip()->top($parameters);
    }

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'top')) {
            $method = $parameters[] = Str::camel(substr($method, 3));

            if (in_array($method, ['event', 'browser', 'os', 'browserLanguage'])) {
                return $this->visiterTop(...$parameters);
            } elseif (in_array($method, ['ip_address', 'country', 'country_code', 'city'])) {
                return $this->ipTop(...$parameters);
            } else {
                throw new BadMethodCallException("Method [$method] does not exist on any Model.");
            }
        }

        return $this->$method(...$parameters);
    }
}
