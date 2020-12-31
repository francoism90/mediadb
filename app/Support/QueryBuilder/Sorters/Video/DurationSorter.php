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

        // Sort clip duration
        $clips = $query->get()->sortBy(function ($model) {
            $duration = $model->media
                ->where('collection_name', 'clip')
                ->pluck('custom_properties.metadata.duration')
                ->first();

            return $duration ?? 0;
        }, SORT_REGULAR, $descending);

        return $query
            ->when($clips->isNotEmpty(), function ($query) use ($clips) {
                $ids = $clips->pluck('id');
                $idsOrder = $clips->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            });
    }
}
