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

            // Fetch ids of user lists
            $options['id'] = $this->getVideoIdsByLists($request->input('filter.type'));

            return app(SearchForVideos::class)($options);
        }

        return app(GetRandomVideos::class)();
    }

    protected function getVideoIdsByLists(mixed $lists = null): ?array
    {
        $lists = is_string($lists) ? explode(',', $lists) : $lists;

        if (!$lists) {
            return null;
        }

        $user = auth()?->user();

        return Video::active()
            ->when(in_array('favorites', $lists), fn ($query) => $query->userFavorites($user))
            ->when(in_array('following', $lists), fn ($query) => $query->userFollowing($user))
            ->when(in_array('viewed', $lists), fn ($query) => $query->userViewed($user))
            ->pluck('id')
            ->take(500)
            ->all();
    }
}
