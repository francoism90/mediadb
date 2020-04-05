<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Filters\Tag\TypeFilter;
use App\Support\QueryBuilder\Sorts\MediaSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index()
    {
        $defaultSort = AllowedSort::field('name', 'order_column');

        $query = QueryBuilder::for(Tag::class)
            ->allowedAppends(['collect', 'media'])
            ->allowedFilters([
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*', '#'),
            ])
            ->AllowedSorts([
                $defaultSort,
                AllowedSort::custom('media', new MediaSorter()),
                AllowedSort::custom('recommended', new RecommendedSorter()),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return TagResource::collection($query);
    }
}
