<?php

namespace App\Actions\Video;

use App\Actions\Media\UpdateMediaDetails;
use App\Models\Media;
use App\Models\Video;

class UpdateVideoClips
{
    public function __invoke(Video $video, array $data): void
    {
        $video->clips?->each(function (Media $media) use ($data): void {
            app(UpdateMediaDetails::class)($media, $data);
        });
    }
}
