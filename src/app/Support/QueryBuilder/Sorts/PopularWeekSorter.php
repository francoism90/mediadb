<?php

namespace App\Support\QueryBuilder\Sorts;

use CyrildeWit\EloquentViewable\Support\Period;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class PopularWeekSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed|null                            $descending
     * @param string|null                           $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, $descending, string $property = null): Builder
    {
        return $query->withCount(['views' => function ($query) {
            $query->withinPeriod(Period::pastWeeks(1))->uniqueVisitor();
        }])->orderBy('views_count', 'desc');
    }
}
