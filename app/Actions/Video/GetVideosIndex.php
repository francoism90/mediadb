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
        // Scout is limited when providng a simple feed (hopefully this will change).
        if ($request->filled('filter') || $request->filled('sort')) {
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

        if (!$types || !is_array($types)) {
            return null;
        }

        $type = fn (string $key) => in_array($key, $types);

        $user = auth()?->user();

        return Video::active()
            ->when($type('favorites'), fn ($query) => $query->userFavorites($user))
            ->when($type('following'), fn ($query) => $query->userFollowing($user))
            ->when($type('viewed'), fn ($query) => $query->userViewed($user))
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
