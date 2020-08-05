<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Services\TagSyncService;
use App\Support\QueryBuilder\Filters\Collection\MediaFilter;
use App\Support\QueryBuilder\Filters\Collection\TypeFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\NameSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use App\Support\QueryBuilder\Sorts\UpdatedSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class CollectionController extends Controller
{
    /**
     * @var TagSyncService
     */
    protected $tagSyncService;

    /**
     * @return void
     */
    public function __construct(TagSyncService $tagSyncService)
    {
        $this->authorizeResource(Collection::class, 'collection');

        $this->tagSyncService = $tagSyncService;
    }

    /**
     * @return CollectionResource
     */
    public function index()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $collections = QueryBuilder::for(Collection::class)
            ->allowedAppends(['items', 'thumbnail_url'])
            ->allowedIncludes(['collection', 'model', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('media', new MediaFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new TypeFilter())->ignore(null, '*'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new NameSorter())->defaultDirection('asc'),
                AllowedSort::custom('recent', new RecentSorter())->defaultDirection('desc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated', new UpdatedSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return CollectionResource::collection($collections);
    }

    /**
     * @param Collection $collection
     *
     * @return CollectionResource
     */
    public function show(Collection $collection)
    {
        // Tracking
        $collection->recordActivity('viewed');
        $collection->recordView('view_count', now()->addYear());

        return new CollectionResource(
            $collection->load(['model', 'tags'])
                       ->append('items')
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Collection    $collection
     *
     * @return CollectionResource
     */
    public function update(UpdateRequest $request, Collection $collection)
    {
        // Set attributes
        $collection->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        // Set status
        if ($request->has('status')) {
            $collection->setStatus($request->input('status'), 'user request');
        }

        // Sync tags
        $this->tagSyncService->sync(
            $collection,
            $request->input('tags')
        );

        return new CollectionResource($collection);
    }

    /**
     * @param Collection $collection
     *
     * @return CollectionResource
     */
    public function destroy(Collection $collection)
    {
        if ($collection->delete()) {
            return new CollectionResource($collection);
        }

        return response()->json('Unable to delete collection', 500);
    }
}
