<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\QueryBuilder\Filters\Filter;
use Str;

class RelatedFilter implements Filter
{
    protected const NAME_REGEX = '~[^\p{L}]++~u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(',', $value) : $value;

        // Find model by hash
        $model = $query->getModel()->findByHash($value);

        // Merge collection models
        $models = collect();

        $models = $models->merge($this->getModelsByQuery($query, $model));
        $models = $models->merge($this->getModelsWithTags($query, $model));

        // Return query
        $ids = $models->pluck('id')->toArray();
        $idsOrder = implode(',', $ids);

        return $query
            ->where('id', '<>', $model->id)
            ->whereIn('id', $ids)
            ->orderByRaw("FIELD(id, {$idsOrder})");
    }

    /**
     * @param Builder $query
     * @param Model   $model
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, Model $model): Collection
    {
        $value = Str::of($model->name)->replaceMatches(self::NAME_REGEX, ' ')->trim();

        return $query
            ->getModel()
            ->search("name:{$value}")
            ->take(12)
            ->get();
    }

    /**
     * @param Builder $query
     * @param Model   $model
     *
     * @return Collection
     */
    protected function getModelsWithTags(Builder $query, Model $model): Collection
    {
        return $query
            ->getModel()
            ->withAnyTagsOfAnyType($model->tags)
            ->inRandomSeedOrder()
            ->take(12)
            ->get();
    }

    /**
     * @param Builder $query
     * @param Model   $model
     *
     * @return Collection
     */
    protected function getModelsWithCollection(Builder $query, Model $model)
    {
        return $query
            ->getModel()
            ->withAnyCollectionsOfAnyType($model->collections)
            ->inRandomSeedOrder()
            ->take(12)
            ->get();
    }
}
