<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visiter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'event',
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
                $query->where('is->bot', false);
            })
            ->when(in_array('ajax', $fields), function ($query) {
                $query->where('is->ajax', false);
            });
    }

    /**
     * Return only visits from bots/crawlers
     *
     * @param  Builder  $query
     * @return void
     */
    public function scopeBots($query)
    {
        $query->where('is->bot', true);
    }

    /**
     * Return only ajax requests
     *
     * @param  Builder  $query
     * @return void
     */
    public function scopeAjax($query)
    {
        $query->where('is->ajax', true);
    }

    /**
     * Return only unique (by ip) visitors
     *
     * @param Builder $query
     * @return void
     */
    public function scopeUnique($query)
    {
        $query->groupBy('ip');
    }

    /**
     * Filter results by the 'created_at' field to fetch records between 2 dates
     *
     * @param Builder $query
     * @param Carbon  $from
     * @param Carbon  $to
     * @return void
     */
    public function scopePeriod($query, $from = null, $to = null)
    {
        $query->whereBetween('created_at', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
    }

    /**
     * Get the top values for a given field
     *
     * @param Carbon  $from
     * @param Carbon  $to
     * @param string  $top
     * @param int  $limit
     * @param  array|mixed  $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public static function top($from, $to, $top, $limit = 10, $columns = ['*'])
    {
        return self::query()
            ->period($from, $to)
            ->except(['ajax', 'bots'])
            ->addSelect(array_merge($columns, [DB::raw("COUNT($top) as '{$top}_count'")]))
            ->latest($top . '_count')
            ->groupBy($top)
            ->limit($limit)
            ->get();
    }

    /**
     * Get all of the ip for the project.
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
}
