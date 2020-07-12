<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Requests\Channel\UpdateRequest;
use App\Http\Resources\ChannelResource;
use App\Models\Channel;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\MostViewsSorter;
use App\Support\QueryBuilder\Sorts\NameSorter;
use App\Support\QueryBuilder\Sorts\PopularMonthSorter;
use App\Support\QueryBuilder\Sorts\PopularWeekSorter;
use App\Support\QueryBuilder\Sorts\RecentSorter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use App\Support\QueryBuilder\Sorts\RelevanceSorter;
use App\Support\QueryBuilder\Sorts\TrendingSorter;
use App\Support\QueryBuilder\Sorts\UpdatedSorter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ChannelController extends Controller
{
    /**
     * @return void
     */
    public function __construct()
    {
        $this->authorizeResource(Channel::class, 'channel');
    }

    /**
     * @return ChannelResource
     */
    public function index()
    {
        $query = Channel::currentStatus(['published']);

        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter())->defaultDirection('desc');

        $channels = QueryBuilder::for($query)
            ->allowedAppends(['thumbnail', 'items'])
            ->allowedIncludes(['media', 'model', 'tags'])
            ->allowedFilters([
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::custom('name', new NameSorter())->defaultDirection('asc'),
                AllowedSort::custom('popular-month', new PopularMonthSorter())->defaultDirection('desc'),
                AllowedSort::custom('popular-week', new PopularWeekSorter())->defaultDirection('desc'),
                AllowedSort::custom('recent', new RecentSorter())->defaultDirection('desc'),
                AllowedSort::custom('relevance', new RelevanceSorter())->defaultDirection('asc'),
                AllowedSort::custom('trending', new TrendingSorter())->defaultDirection('desc'),
                AllowedSort::custom('updated', new UpdatedSorter())->defaultDirection('desc'),
                AllowedSort::custom('views', new MostViewsSorter())->defaultDirection('desc'),
            ])
            ->defaultSort($defaultSort)
            ->jsonPaginate();

        return ChannelResource::collection($channels);
    }

    /**
     * @param Channel $channel
     *
     * @return ChannelResource
     */
    public function show(Channel $channel)
    {
        // Tracking
        $channel->recordActivity('show');
        $channel->recordView('view_count', now()->addYear());

        return new ChannelResource(
            $channel->load(['model', 'tags'])
                    ->append(['thumbnail', 'items'])
        );
    }

    /**
     * @param UpdateRequest $request
     * @param Channel       $channel
     *
     * @return ChannelResource
     */
    public function update(UpdateRequest $request, Channel $channel)
    {
        $channel->update([
            'name' => $request->input('name', $channel->name),
            'description' => $request->input('description', $channel->description),
        ]);

        if ($request->has('status')) {
            $channel->setStatus($request->input('status'), 'user request');
        }

        if ($request->has('tags')) {
            $channel->syncTagsWithTypes($request->input('tags'));
        }

        return new ChannelResource($channel);
    }

    /**
     * @param Channel $channel
     *
     * @return ChannelResource|JsonResponse
     */
    public function destroy(Channel $channel)
    {
        if ($channel->delete()) {
            return new ChannelResource($channel);
        }

        return response()->json('Unable to delete channel', 500);
    }
}
