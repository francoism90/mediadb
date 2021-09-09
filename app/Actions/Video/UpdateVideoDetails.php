<?php

namespace App\Actions\Video;

use App\Actions\Tag\SyncTagsWithTypes;
use App\Events\Video\HasBeenUpdated;
use App\Models\Video;

class UpdateVideoDetails
{
    public function execute(Video $video, array $data): Video
    {
        $collect = collect($data);

        $locale = $collect->get('locale', app()->getLocale());

        $video
            ->setTranslation('name', $locale, $collect->get('name', $video->name))
            ->setTranslation('overview', $locale, $collect->get('overview', $video->overview))
            ->setAttribute('status', $collect->get('status', $video->status))
            ->setAttribute('episode_number', $collect->get('episode_number', $video->episode_number))
            ->setAttribute('season_number', $collect->get('season_number', $video->season_number))
            ->saveOrFail();

        app(SyncTagsWithTypes::class)->execute(
            $video, $collect->get('tags', [])
        );

        event(new HasBeenUpdated($video));

        return $video;
    }
}
