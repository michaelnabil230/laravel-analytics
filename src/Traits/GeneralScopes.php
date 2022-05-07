<?php

namespace MichaelNabil230\LaravelAnalytics\Traits;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

trait GeneralScopes
{
    /**
     * Filter results by the 'created_at' field to fetch records between 2 dates
     *
     * @param Builder $query
     * @param Carbon $from
     * @param Carbon $to
     * @return void
     */
    public function scopePeriod($query, $from, $to)
    {
        $query->whereBetween('created_at', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d 23:59:59')]);
    }

    /**
     * Get the top values for a given field
     *
     * @param Builder $query
     * @param string $top
     * @param array|mixed $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function scopeTop($query, $top, $columns = ['*'])
    {
        return $query
            ->addSelect(array_merge($columns, [DB::raw("COUNT($top) as '{$top}_count'")]))
            ->latest($top . '_count')
            ->groupBy($top);
    }
}
