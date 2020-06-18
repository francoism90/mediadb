<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Playlist\UpdateRequest;
use App\Http\Resources\PlaylistResource;
use App\Models\Playlist;
use App\Support\QueryBuilder\Filters\Playlist\UserFilter;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class PlaylistController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Playlist::class, 'playlist');
    }

    /**
     * @return PlaylistResource
     */
    public function index()
    {
        $query = Playlist::currentStatus(['published']);

        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $playlists = QueryBuilder::for($query)
            ->allowedIncludes(['playlist', 'model', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('user', new UserFilter())->ignore(null, '*', '#'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('popular-month', new PopularMonthSorter()),
                AllowedSort::custom('popular-week', new PopularWeekSorter()),
                AllowedSort::custom('recent', new RecentSorter()),
                AllowedSort::custom('trending', new TrendingSorter()),
                AllowedSort::custom('views', new MostViewsSorter()),
                AllowedSort::field('name'),
                AllowedSort::field('updated_at'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return PlaylistResource::collection($playlists);
    }

    /**
     * @param Playlist $playlist
     *
     * @return PlaylistResource
     */
    public function show(Playlist $playlist)
    {
        // Tracking
        $playlist->recordActivity('show');
        $playlist->recordView('view_count', now()->addYear());

        return new PlaylistResource($playlist->load(['model', 'tags']));
    }

    /**
     * @param UpdateRequest $request
     * @param Playlist      $playlist
     *
     * @return PlaylistResource
     */
    public function update(UpdateRequest $request, Playlist $playlist)
    {
        $playlist->update([
            'name' => $request->input('name', $playlist->name),
            'description' => $request->input('description', $playlist->description),
        ]);

        if ($request->has('status')) {
            $playlist->setStatus($request->input('status'), 'user request');
        }

        if ($request->has('tags')) {
            $playlist->syncTagsWithTypes($request->input('tags'));
        }

        return new PlaylistResource($playlist);
    }

    /**
     * @param Playlist $playlist
     *
     * @return PlaylistResource
     */
    public function destroy(Playlist $playlist)
    {
        if ($playlist->delete()) {
            return new PlaylistResource($playlist);
        }

        return response()->json('Unable to delete playlist', 500);
    }
}
