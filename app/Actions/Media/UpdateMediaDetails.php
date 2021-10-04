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
            ->setAttribute('name', $collect->get('name', $media->name))
            ->setCustomProperty('thumbnail', $collect->get('thumbnail', $media->thumbnail))
            ->saveOrFail();

        MediaHasBeenUpdated::dispatch($media);

        return $media;
    }
}
