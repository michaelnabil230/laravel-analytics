<?php

namespace MichaelNabil230\LaravelAnalytics\Traits;

use Illuminate\Support\Facades\DB;

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
        $query->whereBetween('created_at', [$from->format('Y-m-d'), $to->format('Y-m-d')]);
    }

    /**
     * Get the top values for a given field
     *
     * @param Builder $query
     * @param string $top
     * @param int $limit
     * @param array|mixed $columns
     * @return \Illuminate\Database\Eloquent\Collection<int, static>
     */
    public function scopeTop($query, $top, $limit = 10, $columns = ['*'])
    {
        return $query
            ->addSelect(array_merge($columns, [DB::raw("COUNT($top) as '{$top}_count'")]))
            ->latest($top . '_count')
            ->groupBy($top)
            ->limit($limit)
            ->get();
    }
}
