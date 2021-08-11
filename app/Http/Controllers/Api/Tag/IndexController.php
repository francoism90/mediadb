<?php

namespace App\Http\Controllers\Api\Tag;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorters\RandomSorter;
use App\Support\QueryBuilder\Sorters\RelevanceSorter;
use App\Support\QueryBuilder\Sorters\Tag\ItemSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class IndexController extends Controller
{
    public function __invoke()
    {
        $defaultSort = AllowedSort::field('name', 'order_column')
            ->defaultDirection('asc');

        $tags = QueryBuilder::for(Tag::class)
            ->allowedAppends([
                'items',
                'views',
            ])
            ->allowedFilters([
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*'),
                AllowedFilter::exact('type')->ignore(null, '*'),
                AllowedFilter::scope('id', 'with_slug')->ignore(null, '*'),
            ])
            ->AllowedSorts([
                $defaultSort,
                AllowedSort::custom('items', new ItemSorter())->defaultDirection('desc'),
                AllowedSort::custom('random', new RandomSorter())->defaultDirection('asc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('desc'),
                AllowedSort::field('created_at')->defaultDirection('asc'),
                AllowedSort::field('updated_at')->defaultDirection('asc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return TagResource::collection($tags);
    }
}
