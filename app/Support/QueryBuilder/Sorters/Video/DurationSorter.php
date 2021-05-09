<?php

namespace App\Support\QueryBuilder\Sorters\Video;

use Illuminate\Database\Eloquent\Builder;
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
        // Removing existing orderings
        $query->getQuery()->reorder();

        // Get target table
        $table = $query->getModel()->getTable();

        // Sort clip duration
        $models = $query->get()->sortBy(function ($model) {
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
                    ->whereIn("{$table}.id", $ids)
                    ->orderByRaw("FIELD({$table}.id, {$idsOrder})");
            });
    }
}
