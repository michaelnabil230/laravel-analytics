<?php

namespace MichaelNabil230\LaravelAnalytics;

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
}
