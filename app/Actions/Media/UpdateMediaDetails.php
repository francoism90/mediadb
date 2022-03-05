<?php

namespace App\Actions\Media;

use App\Events\Media\MediaHasBeenUpdated;
use App\Models\Media;

class UpdateMediaDetails
{
    public function __invoke(Media $media, array $data): Media
    {
        $value = fn (string $key, mixed $default = null) => data_get($data, $key, $default);

        $media
            ->setAttribute('name', $value('name', $media->name))
            ->setAttribute('collection_name', $value('collection_name', $media->collection_name))
            ->setCustomProperty('thumbnail', $value('thumbnail', $media->thumbnail));

        $media->saveOrFail();

        MediaHasBeenUpdated::dispatch($media);

        return $media;
    }
}
