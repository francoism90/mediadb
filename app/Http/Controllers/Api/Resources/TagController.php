<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Filters\Tag\TypeFilter;
use App\Support\QueryBuilder\Sorts\InOrderSorter;
use App\Support\QueryBuilder\Sorts\MediaCountSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index()
    {
        $defaultSort = AllowedSort::custom('name', new InOrderSorter())->defaultDirection('asc');

        $query = QueryBuilder::for(Tag::class)
            ->allowedAppends(['media'])
            ->allowedFilters([
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*', '#'),
            ])
            ->AllowedSorts([
                $defaultSort,
                AllowedSort::custom('media', new MediaCountSorter())->defaultDirection('desc'),
                AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return TagResource::collection($query);
    }
}
