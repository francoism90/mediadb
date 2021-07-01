<?php

namespace App\Support\QueryBuilder\Sorters\Video;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $query->getQuery()->reorder();

        $table = $query->getModel()->getTable();

        // Sort by clip duration
        $models = $query->take(1500)->get()->sortBy(function ($model) {
            $duration = $model->media
                ->where('collection_name', 'clip')
                ->pluck('custom_properties.metadata.duration')
                ->first();

            return $duration ?? 0;
        }, SORT_NUMERIC, $descending);

        return $query
            ->when($models->isNotEmpty(), function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }
}
