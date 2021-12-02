<?php

namespace App\Actions\Tag;

use App\Http\Requests\Tag\IndexRequest;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder as ScoutBuilder;
use Illuminate\Support\Arr;

class GetTagsIndex
{
    public function __invoke(IndexRequest $request): EloquentBuilder | ScoutBuilder
    {
        $options = $request->validated();

        // We only use Laravel Scout for filtering and sorting.
        // Scout is limited when providing a simple feed (hopefully this will change).
        $useSearch = $this->useSearchEngine($request, $options);

        if ($useSearch) {
            return app(SearchForTags::class)($options);
        }

        return app(GetTagsFeed::class)($options);
    }

    protected function useSearchEngine(IndexRequest $request, array $options): bool
    {
        // Except filters that can be included on all requests
        $filters = Arr::except($options, ['page', 'size', 'type']);

        return $request->anyFilled(
            array_keys($filters)
        );
    }
}
