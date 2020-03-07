<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Support\QueryBuilder\Filters\Collection\TypeFilter;
use App\Support\QueryBuilder\Filters\HashidFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\ViewedAtFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionController extends Controller
{
    /**
     * @return CollectionResource
     */
    public function index()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $query = QueryBuilder::for(Collection::class)
            ->allowedIncludes(['media', 'tags', 'user'])
            ->allowedFilters([
                AllowedFilter::custom('id', new HashidFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
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
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return CollectionResource::collection($query);
    }

    /**
     * @param Collection $media
     *
     * @return CollectionResource
     */
    public function show(Collection $collect)
    {
        // Tracking
        $collect->recordView('history', now()->addSeconds(30));
        $collect->recordView('view_count', now()->addYear());

        return new CollectionResource(
            $collect->load(['tags'])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Collection    $collect
     *
     * @return CollectionResource
     */
    public function update(UpdateRequest $request, Collection $collect)
    {
        $collect->update([
            'name' => $request->get('name', $collect->name),
            'description' => $request->get('description', $collect->description),
        ]);

        if ($request->has('status')) {
            $collect->setStatus($request->status, 'user request');
        }

        if ($request->has('tags')) {
            $collect->syncTagsWithTypes($request->tags);
        }

        return new CollectionResource($collect);
    }

    /**
     * @param Collection $collect
     *
     * @return CollectionResource
     */
    public function destroy(Collection $collect)
    {
        if ($collect->delete()) {
            return new CollectionResource($collect);
        }

        return response()->json('error', 500);
    }
}
