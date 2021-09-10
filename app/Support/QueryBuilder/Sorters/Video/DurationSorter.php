<?php

namespace App\Support\QueryBuilder\Sorters\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
{
    public const QUERY_LIMIT = 1500;

    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->reorder();

        $table = $query->getModel()->getTable();

        $models = $this->sortModelDurations($query, $descending);

        return $query
            ->when($models->isNotEmpty(), function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }

    protected function sortModelDurations(Builder $query, bool $descending): Collection
    {
        return $query
            ->take(self::QUERY_LIMIT)
            ->get()
            ->sortBy(function (Video $video) {
                return $video
                    ->getFirstMedia('clips')
                    ->getCustomProperty('duration', 0);
            }, SORT_NUMERIC, $descending);
    }
}
