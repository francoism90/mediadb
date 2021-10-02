<?php

namespace App\Support\QueryBuilder\Filters\Video;

use App\Actions\Video\GetSimilarVideos;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\Filters\Filter;

class SimilarFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $model = static::retrieveModel($value);

        $models = static::getQueryCache($model);

        $table = static::getModelTable($query);

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

    protected static function getQueryCache(Model $model): Collection
    {
        $key = sprintf('similar_%s_%s', $model->getTable(), $model->id);

        return Cache::tags($model->getTable())->remember(
            $key, 3600, fn () => static::getQueryResults($model)
        );
    }

    protected static function getQueryResults(Model $model): Collection
    {
        return app(GetSimilarVideos::class)($model)->map->only(['id', 'name']);
    }

    protected static function retrieveModel(string $value): Model
    {
        return Video::findByPrefixedIdOrFail($value);
    }

    protected static function getModelTable(Builder $query): string
    {
        return $query->getModel()->getTable();
    }
}
