<?php

namespace App\Support\QueryBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\PrefixedIds\PrefixedIds;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public const NAME_NUMBER_REGEX = '/[\p{N}]+/u';

    public const NAME_WORD_REGEX = '/[\p{L}]+/u';

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

        // Get Model
        $model = PrefixedIds::find($value);

        throw_if(!$model, InvalidFilterValue::class);

        // Merge results
        $matches = $this->getQueryModels($query, $model);

        $matches = $matches->merge($this->getTaggedModels($query, $model));

        // Build query
        return $query
            ->when($matches->isNotEmpty(), function ($query) use ($matches) {
                $ids = $matches->pluck('id');
                $idsOrder = $matches->implode('id', ',');

                return $query
                    ->whereIn('id', $ids)
                    ->orderByRaw("FIELD(id, {$idsOrder})");
            }, function ($query) {
                return $query->whereNull('id'); // force empty result
            })
            ->where('id', '<>', $model->id)
            ->take(50)
            ->orderBy('id');
    }

    protected function getQueryModels(Builder $query, Model $model): Collection
    {
        $modelName = Str::ascii($model->name);

        // Sort letters before numbers
        $queryValue = Str::of($modelName)->matchAll(self::NAME_WORD_REGEX);

        $queryValue = $queryValue->merge(
            Str::of($modelName)->matchAll(self::NAME_NUMBER_REGEX)
        );

        $collect = collect();

        // Inverse name searching (e.g. this book 1, this book, this)
        for ($i = $queryValue->take(8)->count(); $i >= 1; --$i) {
            $value = $queryValue->take($i)->implode(' ');

            if (strlen($value) <= 1) {
                continue;
            }

            $collect = $collect->merge(
                $query
                    ->getModel()
                    ->search($value)
                    ->take(8)
                    ->get()
            );
        }

        return $collect;
    }

    protected function getTaggedModels(Builder $query, Model $model): Collection
    {
        return $query
            ->getModel()
            ->with('tags')
            ->withAnyTagsOfAnyType(
                $model->tags ?? []
            )
            ->inRandomSeedOrder()
            ->take(25)
            ->get();
    }
}
