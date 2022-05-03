<?php

namespace MichaelNabil230\LaravelAnalytics\Models;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use MichaelNabil230\LaravelAnalytics\Events;

class Analytics extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'event',
        'user_id',
        'ip',
        'method',
        'is_ajax',
        'url',
        'referer',
        'user_agent',
        'is_desktop',
        'is_mobile',
        'is_bot',
        'bot',
        'os_family',
        'os',
        'browser_family',
        'browser',
        'country',
        'browser_language_family',
        'browser_language',
        'country_code',
        'city',
        'latitude',
        'longitude',
    ];

    /**
     * The event map for the model.
     *
     * Allows for object-based events for native Eloquent events.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => Events\SavingAnalytics::class,
        'saved' => Events\AnalyticsSaved::class,
        'creating' => Events\CreatingAnalytics::class,
        'created' => Events\AnalyticsCreated::class,
        'updating' => Events\UpdatingAnalytics::class,
        'updated' => Events\AnalyticsUpdated::class,
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string,int>
     */
    protected $casts = [
        'is_ajax' => 'boolean',
        'is_bot' => 'boolean',
        'is_mobile' => 'boolean',
    ];

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
                $query->where('is_bot', false);
            })
            ->when(in_array('ajax', $fields), function ($query) {
                $query->where('is_ajax', false);
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
        $query->where('is_bot', true);
    }

    /**
     * Return only ajax requests
     *
     * @param  Builder  $query
     * @return void
     */
    public function scopeAjax($query)
    {
        $query->where('is_ajax', true);
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
            ->addSelect(DB::raw("count('$top') as {$top}_count"))
            ->latest($top . '_count')
            ->groupBy($top)
            ->limit($limit)
            ->get($columns);
    }
}
