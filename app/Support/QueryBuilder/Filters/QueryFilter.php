<?php

namespace App\Support\QueryBuilder\Filters;

use App\Actions\Search\QueryDocuments;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class QueryFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $models = $this->getQueryCache($query->getModel(), $value);

        $table = $query->getModel()->getTable();

        return $query
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

    protected function getQueryCache(Model $model, string $value, int $ttl = 600): Collection
    {
        $key = sprintf('documentQuery_%s', Str::camel($value));

        return Cache::remember($key, $ttl, fn () => $this->getQueryResults($model, $value));
    }

    protected function getQueryResults(Model $model, string $value): Collection
    {
        return app(QueryDocuments::class)($model, $value);
    }
}
