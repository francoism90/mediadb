<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\StoreRequest;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Support\QueryBuilder\Filters\CollectionFilter;
use App\Support\QueryBuilder\Filters\HashidFilter;
use App\Support\QueryBuilder\Filters\MediaTypeFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\RelatedFilter;
use App\Support\QueryBuilder\Filters\ViewedAtFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class MediaController extends Controller
{
    /**
     * @return MediaResource
     */
    public function index(Request $request)
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $query = QueryBuilder::for(Media::class)
            ->allowedAppends(['collections', 'download_url', 'stream_url'])
            ->allowedIncludes(['collections', 'model', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('id', new HashidFilter())->ignore(null, '*'),
                AllowedFilter::custom('collect', new CollectionFilter())->ignore(null, '*'),
                AllowedFilter::custom('related', new RelatedFilter())->ignore(null, '*'),
                AllowedFilter::custom('type', new MediaTypeFilter())->ignore(null, '*'),
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
            return MediaResource::collection($query->jsonPaginate());
        }

        return new MediaResource($query->first());
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

        if ($request->has('collect')) {
            $media->syncCollections($request->collect, Auth::user());
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
            return new MediaResource($media);
        }

        return response()->json('error', 500);
    }
}
