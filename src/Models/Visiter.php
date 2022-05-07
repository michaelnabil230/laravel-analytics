<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use MichaelNabil230\LaravelAnalytics\Traits\GeneralScopes;

class Visiter extends Model
{
    use GeneralScopes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'type_request',
        'event',
        'event_description',
        'session_visiter_id',
        'method',
        'url',
        'referer',
        'user_agent',
        'is',
        'bot',
        'os_family',
        'os',
        'browser_family',
        'browser',
        'browser_language_family',
        'browser_language',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,int>
     */
    protected $casts = [
        'is' => 'array',
    ];

    /**
     * Get the sessionVisiter that owns the Visiter
     *
     * @return BelongsTo
     */
    public function sessionVisiter(): BelongsTo
    {
        return $this->belongsTo(SessionVisiter::class);
    }

    /**
     * Get ip by sessionVisiter.
     */
    public function ip()
    {
        return $this->hasOneThrough(
            Ip::class,
            SessionVisiter::class,
            'id',
            'id',
            'session_visiter_id',
            'ip_id',
        );
    }

    /**
     * Scope a query to exclude rows where certain boolean fields are equal to true.
     *
     * @param Builder $query
     * @param array $fields
     * @return void
     */
    public function scopeExcept($query, $fields)
    {
        $query
            ->when(in_array('bots', $fields), function ($query) {
                $query->isBot(false);
            })
            ->when(in_array('ajax', $fields), function ($query) {
                $query->isAjax(false);
            });
    }

    /**
     * Return only visits from bots/crawlers
     *
     * @param Builder $query
     * @param bool $isBot
     * @return void
     */
    public function scopeIsBot($query, $isBot = true)
    {
        $query->where('is->bot', $isBot);
    }

    /**
     * Return only ajax requests
     *
     * @param  Builder  $query
     * @param  bool  $isAjax
     * @return void
     */
    public function scopeIsAjax($query, $isAjax = true)
    {
        $query->where('is->ajax', $isAjax);
    }

    /**
     * Return only uniqueSession (by session_visiter_id) visitors
     *
     * @param Builder $query
     * @return void
     */
    public function scopeUniqueSession($query)
    {
        $query->groupBy('session_visiter_id');
    }

    /**
     * Return only uniqueIp (by ip) visitors
     *
     * @param Builder $query
     * @return void
     */
    public function scopeUniqueIp($query)
    {
        $query->with(['ip' => function ($query) {
            $query->groupBy('ip');
        }]);
    }
}
