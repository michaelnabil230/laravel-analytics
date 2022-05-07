<?php

namespace MichaelNabil230\LaravelAnalytics\Traits;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

trait GeneralScopes
{
    /**
     * Filter results by the 'created_at' field to fetch records between 2 dates
     *
     * @param Builder $query
     * @param Carbon $from
     * @param Carbon $to
     * @return Builder
     */
    public function scopePeriod($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from->format('Y-m-d H:i:s'), $to->format('Y-m-d 23:59:59')]);
    }

    /**
     * Get the top values for a given field
     *
     * @param Builder $query
     * @param string $top
     * @param array|mixed $columns
     * @return Builder
     */
    public function scopeTop($query, $top, $columns = ['*'])
    {
        return $query
            ->select(array_merge($columns, [DB::raw("COUNT($top) as '{$top}_count'")]))
            ->latest($top . '_count')
            ->groupBy($top);
    }
}
