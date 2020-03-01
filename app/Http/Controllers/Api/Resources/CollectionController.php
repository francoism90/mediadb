<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Support\QueryBuilder\Filters\CollectionTypeFilter;
use App\Support\QueryBuilder\Filters\HashidFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\ViewedAtFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionController extends Controller
{
    /**
     * @return CollectionResource
     */
    public function index(Request $request)
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $query = QueryBuilder::for(Collection::class)
            ->allowedIncludes(['tags', 'user'])
            ->allowedFilters([
                AllowedFilter::custom('id', new HashidFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new CollectionTypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*'),
                AllowedFilter::custom('viewed_at', new ViewedAtFilter())->ignore(null),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('popular-month', new PopularMonthSorter()),
                AllowedSort::custom('popular-week', new PopularWeekSorter()),
                AllowedSort::custom('recent', new RecentSorter()),
                AllowedSort::custom('trending', new TrendingSorter()),
                AllowedSort::custom('views', new MostViewsSorter()),
            ])
            ->defaultSort($defaultSort);

        if ($request->has('page.size')) {
            return CollectionResource::collection($query->jsonPaginate());
        }

        return new CollectionResource($query->first());
    }
}
