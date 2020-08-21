<?php

namespace App\Support\QueryBuilder\Sorts\Video;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
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
        $query->getQuery()->orders = null;

        $direction = $descending ? 'DESC' : 'ASC';

        $models = $this->getModels($query, $direction);

        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query->whereIn('id', $ids)
                     ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param Builder $query
     *
     * @return Collection
     */
    protected function getModels(Builder $query, string $direction): Collection
    {
        $filterIds = $query->pluck('id')->toArray();

        return $query
            ->getModel()
            ->search('*')
            ->select('id')
            ->whereIn('id', $filterIds)
            ->collapse('id')
            ->from(0)
            ->take(10000)
            ->orderBy('duration', $direction)
            ->get();
    }
}
