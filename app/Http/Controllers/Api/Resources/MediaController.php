<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\TagSyncService;
use App\Support\QueryBuilder\Filters\Media\ChannelFilter;
use App\Support\QueryBuilder\Filters\Media\CollectionFilter;
use App\Support\QueryBuilder\Filters\Media\RelatedFilter;
use App\Support\QueryBuilder\Filters\Media\WatchedFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\Media\LongestSorter;
use App\Support\QueryBuilder\Sorts\Media\ShortestSorter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\NameSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller
{
    /**
     * @var TagSyncService
     */
    protected $tagSyncService;

    public function __construct(TagSyncService $tagSyncService)
    {
        $this->authorizeResource(Media::class, 'media');

        $this->tagSyncService = $tagSyncService;
    }

    /**
     * @return MediaResource
     */
    public function index()
    {
        $query = Media::currentStatus(['processed', 'public']);

        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $media = QueryBuilder::for($query)
            ->allowedAppends(['preview_url', 'thumbnail_url'])
            ->allowedIncludes(['model', 'collections', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('channel', new ChannelFilter())->ignore(null, '*'),
                AllowedFilter::custom('collection', new CollectionFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('history', new WatchedFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('shortest', new ShortestSorter())->defaultDirection('asc'),
                AllowedSort::custom('longest', new LongestSorter())->defaultDirection('desc'),
                AllowedSort::custom('name', new NameSorter())->defaultDirection('asc'),
                AllowedSort::custom('recent', new RecentSorter())->defaultDirection('desc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return MediaResource::collection($media);
    }

    /**
     * @param Media $media
     *
     * @return MediaResource
     */
    public function show(Media $media)
    {
        // Tracking
        $media->recordActivity('viewed');
        $media->recordView('view_count', now()->addYear());

        return new MediaResource(
            $media->load(['model', 'tags'])
                  ->append([
                      'download_url',
                      'sprite_url',
                      'stream_url',
                      'thumbnail_url',
                    ])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Media         $media
     *
     * @return MediaResource
     */
    public function update(UpdateRequest $request, Media $media)
    {
        // Set attributes
        $media->update([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
        ]);

        // Set model
        $model = Media::findByHash(
            $request->input('model.id', $media->model_id),
            $request->input('model.type', $media->model_type),
        );

        $media->model()->associate($model)->save();

        // Set status
        if ($request->has('status')) {
            $media->setStatus($request->input('status'), 'user-request');
        }

        // Sync tags
        $this->tagSyncService->sync(
            $media,
            $request->input('tags')
        );

        return new MediaResource($media);
    }

    /**
     * @param Media $media
     *
     * @return MediaResource
     */
    public function destroy(Media $media)
    {
        if ($media->delete()) {
            return new MediaResource($media);
        }

        return response()->json('Unable to delete media', 500);
    }
}
