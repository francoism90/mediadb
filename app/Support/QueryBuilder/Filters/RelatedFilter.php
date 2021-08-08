<?php

namespace App\Support\QueryBuilder\Filters;

use App\Services\SearchService;
use Illuminate\Database\Eloquent\Builder;
use Spatie\PrefixedIds\PrefixedIds;
use Spatie\QueryBuilder\Exceptions\InvalidFilterValue;
use Spatie\QueryBuilder\Filters\Filter;

class RelatedFilter implements Filter
{
    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $value = is_array($value) ? implode(' ', $value) : $value;

        $model = PrefixedIds::find($value);
        throw_if(!$model, InvalidFilterValue::class);

        $models = (new SearchService())->excerptSearch($query->getModel(), $model->name ?? '');
        $models = $models->merge((new SearchService())->getTagResults($query->getModel(), $model->tags ?? []));

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
            })
            ->where('id', '<>', $model->id);
    }
}
