<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\TagSyncService;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\RelatedFilter;
use App\Support\QueryBuilder\Filters\Video\CollectionFilter;
use App\Support\QueryBuilder\Sorts\FieldSorter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use App\Support\QueryBuilder\Sorts\Video\DurationSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class VideoController extends Controller
{
    /**
     * @var TagSyncService
     */
    protected $tagSyncService;

    public function __construct(TagSyncService $tagSyncService)
    {
        $this->authorizeResource(Video::class, 'video');

        $this->tagSyncService = $tagSyncService;
    }

    /**
     * @return VideoResource
     */
    public function index()
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $videos = QueryBuilder::for(Video::class)
            ->allowedAppends([
                'metadata',
                'preview_url',
                'sprite_url',
                'stream_url',
                'thumbnail_url',
            ])
            ->allowedIncludes([
                'collections',
                'tags',
            ])
            ->allowedFilters([
                AllowedFilter::custom('collection', new CollectionFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new FieldSorter())->defaultDirection('asc'),
                AllowedSort::custom('created_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated_at', new FieldSorter())->defaultDirection('desc'),
                AllowedSort::custom('duration', new DurationSorter())->defaultDirection('asc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return VideoResource::collection($videos);
    }

    /**
     * @param Video $video
     *
     * @return VideoResource
     */
    public function show(Video $video)
    {
        // Tracking
        $video->recordActivity('viewed');
        $video->recordView('view_count', now()->addYear());

        return new VideoResource(
            $video->load('tags')
                  ->append([
                    'metadata',
                    'sprite_url',
                    'stream_url',
                    'thumbnail_url',
                ])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Video         $video
     *
     * @return VideoResource
     */
    public function update(UpdateRequest $request, Video $video)
    {
        // Set attributes
        $video->update([
            'name' => $request->input('name'),
            'overview' => $request->input('overview'),
        ]);

        // Set status
        $video->setStatus($request->input('status', 'released'));

        // Sync tags
        $this->tagSyncService->sync(
            $video,
            $request->input('tags')
        );

        return new VideoResource($video);
    }

    /**
     * @param Video $video
     *
     * @return VideoResource
     */
    public function destroy(Video $video)
    {
        if ($video->delete()) {
            return new VideoResource($video);
        }

        return response()->json('Unable to delete video', 500);
    }
}
