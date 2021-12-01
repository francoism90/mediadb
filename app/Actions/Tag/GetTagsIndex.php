<?php

namespace App\Actions\Tag;

use App\Http\Requests\Tag\IndexRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder as ScoutBuilder;

class GetTagsIndex
{
    public function __invoke(IndexRequest $request): EloquentBuilder | ScoutBuilder
    {
        // We only use Laravel Scout for filtering and sorting.
        // Scout is limited when providng a simple feed (hopefully this will change).
        if ($request->filled('filter') || $request->filled('sort')) {
            $options = $request->validated();

            return app(SearchForTags::class)($options);
        }

        return app(GetRandomTags::class)();
    }
}
