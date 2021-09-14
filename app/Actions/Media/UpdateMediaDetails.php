<?php

namespace App\Actions\Media;

use App\Events\Media\MediaHasBeenUpdated;
use App\Models\Media;

class UpdateMediaDetails
{
    public function __invoke(Media $media, array $data): Media
    {
        $collect = collect($data);

        $media
            ->forgetCustomProperty('thumbnail')
            ->setCustomProperty('thumbnail', $collect->get('thumbnail', $media->thumbnail))
            ->saveOrFail();

        event(new MediaHasBeenUpdated($media));

        return $media;
    }
}
