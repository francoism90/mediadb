<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public const NAME_REGEX = '/[\p{L}]+/u';

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        // Convert values to models
        $models = is_string($value) ? explode(',', $value) : $value;

        $models = $query->getModel()->convertHashidsToModels($models);

        // Merge related model matches
        $matches = $this->getModelsByQuery($query, $models);

        $matches = $matches->merge($this->getModelsByTags($query, $models));

        return $query
            ->when($models->isNotEmpty(), function ($query) use ($matches) {
                $ids = $matches->pluck('id');
                $idsOrder = $matches->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            }, function ($query) {
                return $query->whereNull('id'); // force empty result
            })
            ->whereNotIn('id', $models->pluck('id'))
            ->take(50)
            ->orderBy('id');
    }

    /**
     * @param Builder    $query
     * @param Collection $models
     *
     * @return Collection
     */
    protected function getModelsByTags(Builder $query, Collection $models): Collection
    {
        return $query
            ->getModel()
            ->with('tags')
            ->withAnyTagsOfAnyType(
                $models->pluck('tags.*.name')->collapse()
            )
            ->inRandomSeedOrder()
            ->take(25)
            ->get();
    }

    /**
     * @param Builder    $query
     * @param Collection $models
     *
     * @return Collection
     */
    protected function getModelsByQuery(Builder $query, Collection $models): Collection
    {
        $names = Str::of($models->pluck('name'))->matchAll(self::NAME_REGEX);

        $collect = collect();

        // Inverse name searching (e.g. this book 1, this book, this)
        for ($i = $names->take(8)->count(); $i >= 1; --$i) {
            $value = $names->take($i)->implode(' ');

            if (strlen($value) <= 1) {
                continue;
            }

            $collect = $collect->merge(
                $query
                    ->getModel()
                    ->search($value)
                    ->take(4)
                    ->get()
            );
        }

        return $collect;
    }
}
