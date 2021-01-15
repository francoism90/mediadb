<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public const NAME_REGEX = '/[\p{N}\p{L}]+/u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $models = is_string($value) ? explode(',', $value) : $value;

        $models = $query->getModel()->convertHashidsToModels($value);

        // Force empty result
        if ($models->isEmpty()) {
            return $query->whereNull('id');
        }

        $relatedModels = $this->getQueryModels($query, $models);
        $relatedModels = $relatedModels->merge($this->getTagsModels($query, $models));

        return $query
            ->when($relatedModels->isNotEmpty(), function ($query) use ($relatedModels) {
                $ids = $relatedModels->pluck('id');
                $idsOrder = $relatedModels->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            })
            ->take(50)
            ->orderBy('id');
    }

    /**
     * @param Builder    $query
     * @param Collection $models
     *
     * @return Collection
     */
    protected function getTagsModels(Builder $query, Collection $models): Collection
    {
        return $query
            ->getModel()
            ->with('tags')
            ->withAnyTagsOfAnyType(
                $models->pluck('tags')->collapse()
            )
            ->whereNotIn('id', $models->pluck('id'))
            ->inRandomSeedOrder()
            ->take(12)
            ->get();
    }

    /**
     * @param Builder    $query
     * @param Collection $models
     *
     * @return Collection
     */
    protected function getQueryModels(Builder $query, Collection $models): Collection
    {
        $names = Str::of($models->pluck('name'))->matchAll(self::NAME_REGEX);

        $queryString = implode(' ', $names->toArray());

        $matches = $query
            ->getModel()
            ->multiMatchQuery($queryString, 50)
            ->get();

        return $matches
            ->whereNotIn('id', $models->pluck('id'))
            ->take(12);
    }
}
