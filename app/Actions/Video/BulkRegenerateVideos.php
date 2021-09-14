<?php

namespace App\Actions\Video;

use App\Actions\Media\RegenerateMedia;
use App\Models\Media;
use App\Models\Video;
use Illuminate\Support\LazyCollection;

class BulkRegenerateVideos
{
    public function __invoke(): void
    {
        $this->getModels()->each(function (Video $video): void {
            $video->getMedia('clips')->each(
                fn (Media $media) => app(RegenerateMedia::class)($media)
            );
        })->reject(function (Video $video): bool {
            return !$video->hasMedia('clips');
        });
    }

    protected function getModels(): LazyCollection
    {
        return Video::with('media')->cursor();
    }
}
