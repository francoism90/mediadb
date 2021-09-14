<?php

namespace App\Support\QueryBuilder\Filters\Video;

use App\Actions\Video\GetSimilarVideos;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Filters\Filter;

class SimilarFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $value = Video::findByPrefixedId($value);

        $table = $query->getModel()->getTable();

        $models = app(GetSimilarVideos::class)($value);

        return $query
            ->where('id', '<>', $value->id)
            ->when($models->isEmpty(), function ($query) use ($table) {
                return $query->whereNull(sprintf('%s.id', $table));
            }, function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }
}
