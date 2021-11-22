<?php

namespace App\Support\QueryBuilder\Filters;

use App\Actions\Search\QueryDocuments;
use App\Traits\InteractsWithQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
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
        $key = static::getQueryCacheKey($query, sprintf('q-%s', $value));

        return Cache::remember(
            $key, 3600, fn () => static::getQueryResults($query, $value)->map->only('id')
        );
    }

    protected static function getQueryResults(Builder $query, string $value): Collection
    {
        $model = static::getQueryModel($query);

        return app(QueryDocuments::class)($model, $value);
    }
}
