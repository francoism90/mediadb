<?php

namespace App\Support\QueryBuilder\Filters\Video;

use App\Actions\Video\GetSimilarVideos;
use App\Models\Video;
use App\Traits\InteractsWithQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\Filters\Filter;

class SimilarFilter implements Filter
{
    use InteractsWithQueryBuilder;

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $table = static::getQueryTable($query);

        $results = static::getQueryCache($query, $value);

        return $query
            ->when($results->isEmpty(), function ($query) use ($table) {
                return $query->whereNull(sprintf('%s.id', $table));
            }, function ($query) use ($results, $table) {
                $ids = $results->pluck('id');
                $idsOrder = $results->implode('id', ',');

                return $query
                    ->whereIn(sprintf('%s.id', $table), $ids)
                    ->orderByRaw(sprintf('FIELD(%s.id, %s)', $table, $idsOrder));
            });
    }

    protected static function getQueryCache(Builder $query, string $value): Collection
    {
        $key = static::getQueryCacheKey($query, "s-{$value}");

        $model = Video::findByPrefixedIdOrFail($value);

        return Cache::remember(
            $key, 3600, fn () => static::getQueryResults($model)
        );
    }

    protected static function getQueryResults(Model $model): Collection
    {
        return app(GetSimilarVideos::class)($model)->map->only('id');
    }
}
