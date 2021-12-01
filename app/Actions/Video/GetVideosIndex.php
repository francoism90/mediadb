<?php

namespace App\Actions\Video;

use App\Http\Requests\Video\IndexRequest;
use App\Models\Video;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Laravel\Scout\Builder as ScoutBuilder;

class GetVideosIndex
{
    public function __invoke(IndexRequest $request): EloquentBuilder | ScoutBuilder
    {
        // We only use Laravel Scout for filtering and sorting.
        // Laravel Scout is limited when creating simple feeds.
        if ($request->has('filter') || $request->has('sort')) {
            $options = $request->validated();

            // Filters
            $types = $request->input('filter.type');

            // Set matching ids of user types (if any)
            $options = data_set($options, 'filter.id', $this->getVideoIdsByType($types));

            return app(SearchForVideos::class)($options);
        }

        return app(GetRandomVideos::class)();
    }

    protected function getVideoIdsByType(mixed $types = null): ?array
    {
        $types = is_string($types) ? explode(',', $types) : $types;

        if (!$types) {
            return null;
        }

        $user = auth()?->user();

        return Video::active()
            ->when(in_array('favorites', $types), fn ($query) => $query->userFavorites($user))
            ->when(in_array('following', $types), fn ($query) => $query->userFollowing($user))
            ->when(in_array('viewed', $types), fn ($query) => $query->userViewed($user))
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
