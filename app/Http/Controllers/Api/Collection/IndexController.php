<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Support\QueryBuilder\Filters\Collection\TypeFilter;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Sorters\FieldSorter;
use App\Support\QueryBuilder\Sorters\MostViewsSorter;
use App\Support\QueryBuilder\Sorters\RecommendedSorter;
use App\Support\QueryBuilder\Sorters\TrendingSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $collections = QueryBuilder::for(Collection::class)
            ->allowedAppends([
                'item_count',
                'thumbnail_url',
            ])
            ->allowedIncludes([
                'tags',
                'videos',
            ])
            ->allowedFilters([
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new FieldSorter())->defaultDirection('asc'),
                AllowedSort::custom('type', new FieldSorter())->defaultDirection('asc'),
                AllowedSort::custom('created_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return CollectionResource::collection($collections);
    }
}
