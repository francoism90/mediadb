<?php

namespace App\Http\Controllers\Api\Resource;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreRequest;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Support\QueryBuilder\Filters\Media\CollectionFilter;
use App\Support\QueryBuilder\Filters\Media\RandomFilter;
use App\Support\QueryBuilder\Filters\Media\RelatedFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\TaggedFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller
{
    /**
     * @return MediaResource
     */
    public function index()
    {
        $query = QueryBuilder::for(Media::class)
            ->allowedIncludes(['model', 'tags'])
            ->allowedSorts([
                AllowedSort::custom('popular-month', new PopularMonthSorter()),
                AllowedSort::custom('popular-week', new PopularWeekSorter()),
                AllowedSort::custom('recent', new RecentSorter()),
                AllowedSort::custom('recommended', new RecommendedSorter()),
                AllowedSort::custom('trending', new TrendingSorter()),
                AllowedSort::custom('views', new MostViewsSorter()),
            ])
            ->allowedFilters([
                AllowedFilter::custom('collection', new CollectionFilter()),
                AllowedFilter::custom('random', new RandomFilter()),
                AllowedFilter::custom('related', new RelatedFilter()),
                AllowedFilter::custom('query', new QueryFilter()),
                AllowedFilter::custom('tags', new TaggedFilter()),
            ])
            ->jsonPaginate();

        return MediaResource::collection($query);
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
        $media->recordView('media');

        return (new MediaResource($media->load(['model', 'tags'])))
            ->additional([
                'data' => [
                    'stream_url' => $media->stream_url,
                    'download' => $media->download_url,
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
            'name' => $request->get('name', $media->name),
            'description' => $request->get('description', $media->description),
        ]);

        if ($request->has('status')) {
            $media->setStatus($request->status, 'user request');
        }

        if ($request->has('tags')) {
            $media->syncTagsWithTypes($request->tags);
        }

        if ($request->has('snapshot')) {
            $media->setCustomProperty('snapshot', $request->snapshot)->save();

            Artisan::call('medialibrary:regenerate', [
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
            return response()->json('success', 200);
        }

        return response()->json('error', 500);
    }
}
