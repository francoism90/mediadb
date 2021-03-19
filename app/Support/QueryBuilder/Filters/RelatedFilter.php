<?php

namespace App\Support\QueryBuilder\Filters;

use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\PrefixedIds\PrefixedIds;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode('', $value) : $value;

        // Find model by given value (if exists)
        $model = PrefixedIds::find($value);

        throw_if(!$model, InvalidFilterValue::class);

        // Merge results
        $models = $this->getModelsByQuery($query, $model);

        $models = $models->merge($this->getModelsByTags($query, $model));

        // Build query
        return $query
            ->when($models->isEmpty(), function ($query) {
                return $query->whereNull('id');
            }, function ($query) use ($models) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            })
            ->where('id', '<>', $model->id)
            ->take(50)
            ->orderBy('id');
    }

    /**
     * @param Builder $query
     * @param string  $value
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, Model $model): Collection
    {
        $searchService = new SearchService();

        $searchService->search($query, $model->name);

        return $searchService->getResults();
    }

    /**
     * @param Builder $query
     * @param array   $value
     *
     * @return Collection
     */
    protected function getModelsByTags(Builder $query, Model $model): Collection
    {
        return $query
            ->getModel()
            ->with('tags')
            ->withAnyTagsOfAnyType(
                $model->tags ?? []
            )
            ->inRandomSeedOrder()
            ->take(50)
            ->get();
    }
}
