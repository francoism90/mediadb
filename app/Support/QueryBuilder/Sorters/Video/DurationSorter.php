<?php

namespace App\Support\QueryBuilder\Sorters\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->reorder();

        $table = $query->getModel()->getTable();

        $models = static::sortModels($query, $descending);

        return $query
            ->when($models->isNotEmpty(), function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }

    protected static function sortModels(Builder $query, bool $descending): Collection
    {
        return $query
            ->take(1000)
            ->get()
            ->sortBy(
                fn (Video $video) => $video->duration ?? 0,
                SORT_NUMERIC,
                $descending
            );
    }
}
