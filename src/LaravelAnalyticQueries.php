<?php

namespace MichaelNabil230\LaravelAnalytics;

use ArrayAccess;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Traits\ForwardsCalls;
use MichaelNabil230\LaravelAnalytics\Models\Ip;
use Illuminate\Database\Eloquent\Relations\Relation;
use MichaelNabil230\LaravelAnalytics\Models\Visiter;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use MichaelNabil230\LaravelAnalytics\Exceptions\InvalidSubject;

class LaravelAnalyticQueries implements ArrayAccess
{
    use ForwardsCalls;

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation */
    protected $subject;

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $subject
     */
    public function __construct($subject)
    {
        $this->initializeSubject($subject);
    }

    /**
     * @param EloquentBuilder|Relation|string $subject
     *
     * @return static
     */
    private static function for($subject): self
    {
        if (is_subclass_of($subject, Model::class)) {
            $subject = $subject::query();
        }

        return new static($subject);
    }

    public static function visiter(): self
    {
        $visiterModel = config('analytics.visiter_model', Visiter::class);

        return self::for(new $visiterModel());
    }

    public static function ip(): self
    {
        $ipModel = config('analytics.ip_model', Ip::class);

        return self::for(new $ipModel());
    }

    public function visiterTop($parameters)
    {
        return $this->subject->except(['ajax', 'bots'])->top($parameters);
    }

    public function ipTop($parameters)
    {
        return $this->subject->top($parameters);
    }

    public function __call($method, $parameters)
    {
        if (Str::startsWith($method, 'top')) {
            $method = Str::camel(substr($method, 3));
            $parameters[] = $method;

            if (in_array($method, ['event', 'browser', 'os', 'browserLanguage'])) {
                return $this->visiterTop(...$parameters);
            } elseif (in_array($method, ['ip_address', 'country', 'country_code', 'city'])) {
                return $this->ipTop(...$parameters);
            }

            throw new BadMethodCallException("Method [$method] does not exist on any Model.");
        }

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

    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Relations\Relation $subject
     *
     * @return $this
     */
    protected function initializeSubject($subject): self
    {
        throw_unless(
            $subject instanceof EloquentBuilder || $subject instanceof Relation,
            InvalidSubject::make($subject)
        );

        $this->subject = $subject;

        return $this;
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
