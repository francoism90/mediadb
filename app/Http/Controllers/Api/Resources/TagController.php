<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Filters\TagTypeFilter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index(Request $request)
    {
        $defaultSort = AllowedSort::field('name', 'order_column');

        $query = QueryBuilder::for(Tag::class)
            ->allowedFilters([
                AllowedFilter::custom('type', new TagTypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*'),
            ])
            ->AllowedSorts([
                $defaultSort,
                AllowedSort::custom('recommended', new RecommendedSorter()),
            ])
            ->defaultSort($defaultSort);

        if ($request->has('page.size')) {
            return TagResource::collection($query->jsonPaginate());
        }

        return new TagResource($query->first());
    }
}
