<?php

namespace App\Actions\Video;

use App\Actions\Tag\SyncTagsWithTypes;
use App\Events\Video\VideoHasBeenUpdated;
use App\Models\Video;

class UpdateVideoDetails
{
    public function __invoke(Video $video, array $data): void
    {
        $collect = collect($data);

        // Update attributes
        $locale = $collect->get('locale', app()->getLocale());

        $video
            ->setTranslation('name', $locale, $collect->get('name', $video->name))
            ->setTranslation('overview', $locale, $collect->get('overview', $video->overview))
            ->setAttribute('status', $collect->get('status', $video->status))
            ->setAttribute('episode_number', $collect->get('episode_number', $video->episode_number))
            ->setAttribute('season_number', $collect->get('season_number', $video->season_number));

        $video->extra_attributes
            ->set('thumbnail', $collect->get('thumbnail', $video->thumbnail));

        $video->saveOrFail();

        // Update clips attributes
        app(UpdateVideoClips::class)($video, [
            'thumbnail' => $collect->get('thumbnail', $video->thumbnail),
        ]);

        // Sync tags
        app(SyncTagsWithTypes::class)($video, $collect->get('tags', []));

        // Dispatch event
        VideoHasBeenUpdated::dispatch(
            $video->refresh()
        );
    }
}
