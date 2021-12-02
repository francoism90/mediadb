<?php

namespace App\Actions\Video;

use App\Http\Requests\Video\IndexRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Arr;
use Laravel\Scout\Builder as ScoutBuilder;

class GetVideosIndex
{
    public function __invoke(IndexRequest $request): EloquentBuilder | ScoutBuilder
    {
        $options = $request->validated();

        // We only use Laravel Scout for filtering and sorting.
        // Scout is limited when providing a simple feed (hopefully this will change).
        $useSearch = $this->useSearchEngine($request, $options);

        if ($useSearch) {
            return app(SearchForVideos::class)($options);
        }

        return app(GetVideosFeed::class)($options);
    }

    protected function useSearchEngine(IndexRequest $request, array $options): bool
    {
        // Except filters that can be included on all requests
        $params = Arr::except($options, ['page', 'size', 'type']);

        return $request->anyFilled($params);
    }
}
