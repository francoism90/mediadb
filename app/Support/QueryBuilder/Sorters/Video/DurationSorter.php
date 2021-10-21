<?php

namespace App\Support\QueryBuilder\Sorters\Video;

use App\Models\Video;
use App\Traits\InteractsWithQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Sorts\Sort;

class DurationSorter implements Sort
{
    use InteractsWithQueryBuilder;

    public function __invoke(Builder $query, bool $descending, string $property): Builder
    {
        $table = static::getQueryTable($query);

        $results = static::getQueryResults($query, $descending);

        return $query
            ->when($results->isEmpty(), function ($query) use ($table) {
                return $query->whereNull(sprintf('%s.id', $table));
            }, function ($query) use ($results, $table) {
                $ids = $results->pluck('id');
                $idsOrder = $results->implode('id', ',');

                return $query
                    ->reorder()
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }

    protected static function getQueryResults(Builder $query, bool $descending): Collection
    {
        $ids = $query->pluck('id');

        $model = static::getQueryModel($query);

        return $model
            ->get()
            ->whereIn('id', $ids)
            ->sortBy(
                fn (Video $video) => $video->duration ?? 0,
                SORT_NUMERIC,
                $descending
            );
    }
}
