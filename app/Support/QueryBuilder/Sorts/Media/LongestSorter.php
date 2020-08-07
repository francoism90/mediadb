<?php

namespace App\Support\QueryBuilder\Sorts\Media;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class LongestSorter implements Sort
{
    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param bool                                  $descending
     * @param string                                $property
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        // Remove any current orders
        $query->getQuery()->orders = null;

        // Search in ES
        $models = $this->getScoutResults($query);

        // Return ids order
        $ids = $models->keys()->all() ?? [];
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @return Collection
     */
    protected function getScoutResults(Builder $query)
    {
        return $query
            ->getModel()
            ->search('*')
            ->select('id')
            ->whereIn('id', $query->pluck('id')->toArray())
            ->collapse('id')
            ->from(0)
            ->take(5000)
            ->orderBy('duration', 'DESC');
    }
}
