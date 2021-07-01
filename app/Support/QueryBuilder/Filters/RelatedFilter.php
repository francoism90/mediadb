<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\PrefixedIds\PrefixedIds;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public const FILTER_NUMBER_REGEX = '/[\p{N}]+/u';
    public const FILTER_PUNCTUATION_REGEX = '/[\p{P}]+/u';
    public const FILTER_WORD_REGEX = '/[\p{L}]+/u';
    public const QUERY_WORD_LIMIT = 8;
    public const QUERY_RESULT_LIMIT = 50;

    /**
     * @param Builder      $query
     * @param string|array $value
     * @param string       $property
     *
     * @return Builder
     */
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $model = PrefixedIds::find($value);
        throw_if(!$model, InvalidFilterValue::class);

        $table = $query->getModel()->getTable();

        $models = $this->getRelatedByQuery($query, $model->name);
        $models = $models->merge($this->getModelsByTags($query, $model->tags));

        return $query
            ->when($models->isEmpty(), function ($query) use ($table) {
                return $query->whereNull("{$table}.id");
            }, function ($query) use ($models, $table) {
                $ids = $models->pluck('id');
                $idsOrder = $models->implode('id', ',');

                return $query
                    ->whereIn("{$table}.id", $ids)
                    ->orderByRaw("FIELD({$table}.id, {$idsOrder})");
            })
            ->where('id', '<>', $model->id)
            ->take(50)
            ->orderBy('id');
    }

    protected function getRelatedByQuery(Builder $query, string $value): Collection
    {
        $models = collect();

        $value = $this->sanitizeQuery($value);

        // e.g. this book 1, this book, this
        for ($i = $value->take(self::QUERY_WORD_LIMIT)->count(); $i >= 1; --$i) {
            $queryValue = $value->take($i)->implode(' ');

            $models = $models->merge(
                $query
                    ->getModel()
                    ->search($queryValue)
                    ->take(self::QUERY_RESULT_LIMIT)
                    ->get()
            );
        }

        return $models;
    }

    protected function getModelsByTags(Builder $query, Collection $tags): Collection
    {
        return $query
            ->getModel()
            ->with('tags')
            ->withAnyTagsOfAnyType($tags)
            ->inRandomSeedOrder()
            ->take(50)
            ->get();
    }

    protected function sanitizeQuery(string $value = ''): Collection
    {
        $query = Str::ascii($value);

        $query = Str::of($value)->matchAll(self::FILTER_WORD_REGEX);

        $query = $query->merge(
            Str::of($value)->matchAll(self::FILTER_NUMBER_REGEX)
        );

        $query = $query->merge(
            Str::of($value)->matchAll(self::FILTER_PUNCTUATION_REGEX)
        );

        return $query;
    }
}
