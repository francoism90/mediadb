<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreRequest;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Http\Resources\PlaylistResource;
use App\Models\Media;
use App\Models\User;
use App\Support\QueryBuilder\Filters\Media\ChannelFilter;
use App\Support\QueryBuilder\Filters\Media\PlaylistFilter;
use App\Support\QueryBuilder\Filters\Media\RelatedFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\Media\LongestSorter;
use App\Support\QueryBuilder\Sorts\Media\ShortestSorter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\NameSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Media::class, 'media');
    }

    /**
     * @return MediaResource
     */
    public function index()
    {
        $query = Media::currentStatus(['processed', 'public']);

        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $media = QueryBuilder::for($query)
            ->allowedIncludes(['model', 'playlists', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('channel', new ChannelFilter())->ignore(null, '*'),
                AllowedFilter::custom('playlist', new PlaylistFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('shortest', new ShortestSorter())->defaultDirection('asc'),
                AllowedSort::custom('longest', new LongestSorter())->defaultDirection('desc'),
                AllowedSort::custom('name', new NameSorter())->defaultDirection('asc'),
                AllowedSort::custom('popular-month', new PopularMonthSorter())->defaultDirection('desc'),
                AllowedSort::custom('popular-week', new PopularWeekSorter())->defaultDirection('desc'),
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
     * @param StoreRequest $request
     *
     * @return MediaResource
     */
    public function store(StoreRequest $request)
    {
        $files = $request->file('files');

        $models = collect();

        foreach ($files as $file) {
            $media = $request->user()
                ->addMedia($file)
                ->usingName($file->getClientOriginalName())
                ->toMediaCollection('videos');

            $media->setStatus('private', 'needs approval');

            $models->push($media);
        }

        return MediaResource::collection($models);
    }

    /**
     * @param Media $media
     *
     * @return MediaResource
     */
    public function show(Media $media)
    {
        // Tracking
        $media->recordActivity('show');
        $media->recordView('view_count', now()->addYear());

        return (new MediaResource($media->load(['model', 'tags'])))
            ->additional([
                'meta' => [
                    'download_url' => $media->download_url,
                    'stream_url' => $media->stream_url,
                    'user_playlists' => PlaylistResource::collection(
                        $media->playlists()
                              ->where('model_type', User::class)
                              ->where('model_id', auth()->user()->id)
                              ->get()
                    ),
                ],
            ]);
    }

    /**
     * @param UpdateRequest $request
     * @param Media         $media
     *
     * @return MediaResource
     */
    public function update(UpdateRequest $request, Media $media)
    {
        $media->update([
            'name' => $request->input('name', $media->name),
            'description' => $request->input('description', $media->description),
        ]);

        if ($request->has('model')) {
            $model = Media::findByHash(
                $request->input('model.id', $media->model_id),
                $request->input('model.type', $media->model_type),
            );

            $media->model()->associate($model)->save();
        }

        if ($request->has('status')) {
            $media->setStatus($request->input('status'), 'update-request');
        }

        if ($request->has('tags')) {
            $media->syncTagsWithTypes($request->input('tags'));
        }

        if ($request->has('playlists')) {
            $request->user()->syncPlaylistsWithMedia($media, $request->input('playlists'));
        }

        if ($request->has('snapshot')) {
            $media->setCustomProperty('snapshot', $request->input('snapshot'))->save();

            Artisan::call('media-library:regenerate', [
                '--ids' => $media->id,
                '--force' => true,
            ]);
        }

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
