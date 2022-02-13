<?php

namespace App\Actions\Video;

use App\Actions\Tag\SyncTagsWithTypes;
use App\Events\Video\VideoHasBeenUpdated;
use App\Models\Video;

class UpdateVideoDetails
{
    public function __invoke(Video $video, array $data): void
    {
        $value = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        // Update attributes
        $locale = $value('locale', app()->getLocale());

        $video
            ->setTranslation('name', $locale, $value('name', $video->name))
            ->setTranslation('overview', $locale, $value('overview', $video->overview))
            ->setAttribute('status', $value('status', $video->status))
            ->setAttribute('episode_number', $value('episode_number', $video->episode_number))
            ->setAttribute('season_number', $value('season_number', $video->season_number));

        $video->extra_attributes
            ->set('thumbnail', $value('thumbnail', $video->thumbnail));

        $video->saveOrFail();

        // Update clips attributes
        app(UpdateVideoClips::class)($video, [
            'thumbnail' => $value('thumbnail', $video->thumbnail),
        ]);

        // Sync tags
        app(SyncTagsWithTypes::class)($video, $value('tags.*.id'));

        // Dispatch event
        VideoHasBeenUpdated::dispatch(
            $video->refresh()
        );
    }
}
