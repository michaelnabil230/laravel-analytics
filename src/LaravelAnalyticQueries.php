<?php

namespace MichaelNabil230\LaravelAnalytics;

use ArrayAccess;
use BadMethodCallException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\ForwardsCalls;
use MichaelNabil230\LaravelAnalytics\Exceptions\InvalidSubject;
use MichaelNabil230\LaravelAnalytics\Models\Ip;
use MichaelNabil230\LaravelAnalytics\Models\SessionVisiter;
use MichaelNabil230\LaravelAnalytics\Models\Visiter;

class LaravelAnalyticQueries implements ArrayAccess
{
    use ForwardsCalls;

    protected Builder|Relation $subject;

    /**
     * @param Builder|Relation $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param Builder|Relation|string $subject
     */
    private static function for($subject): Builder
    {
        if (is_subclass_of($subject, Model::class)) {
            $subject = $subject::query();
        }

        return (new self($subject))->getEloquentBuilder();
    }

    public static function visiter(): Builder
    {
        $model = config('analytics.visiter_model', Visiter::class);

        return self::for(new $model());
    }

    public static function ip(): Builder
    {
        $model = config('analytics.ip_model', Ip::class);

        return self::for(new $model());
    }

    public static function sessionVisiter(): Builder
    {
        $model = config('analytics.session_visiter_model', SessionVisiter::class);

        return self::for(new $model());
    }

    private function getEloquentBuilder(): Builder
    {
        if ($this->subject instanceof Builder) {
            return $this->subject;
        }

        if ($this->subject instanceof Relation) {
            return $this->subject->getQuery();
        }

        throw InvalidSubject::make($this->subject);
    }

    public static function __callStatic($method, $parameters)
    {
        if (! Str::startsWith($method, 'top')) {
            return $method(...$parameters);
        }

        $method = substr($method, 3);

        if (Str::startsWith($method, 'Visiter')) {
            $parentMethod = 'visiter';
        } elseif (Str::startsWith($method, 'Ip')) {
            $parentMethod = 'ip';
        } elseif (Str::startsWith($method, 'SessionVisiter')) {
            $parentMethod = 'sessionVisiter';
        } else {
            throw new BadMethodCallException("Method {$method} not found");
        }

        $parameters[] = Str::camel(substr($method, strlen($parentMethod)));

        return self::$parentMethod()->top(...array_reverse($parameters));
    }

    public function __call($method, $parameters)
    {
        $result = $this->forwardCallTo($this->subject, $method, $parameters);

        /*
         * If the forwarded method call is part of a chain we can return $this
         * instead of the actual $result to keep the chain going.
         */
        if ($result === $this->subject) {
            return $this;
        }

        return $result;
    }

    public function clone()
    {
        return clone $this;
    }

    public function __clone()
    {
        $this->subject = clone $this->subject;
    }

    public function __get($name)
    {
        return $this->subject->{$name};
    }

    public function __set($name, $value)
    {
        $this->subject->{$name} = $value;
    }

    public function offsetExists($offset): bool
    {
        return isset($this->subject[$offset]);
    }

    public function offsetGet($offset): bool
    {
        return $this->subject[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->subject[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->subject[$offset]);
    }
}
