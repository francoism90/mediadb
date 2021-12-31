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
            // Regenerate each clip
            $video->clips?->each(function (Media $media): void {
                app(RegenerateMedia::class)($media);
            });

            // Regenerate the video
            app(RegenerateVideo::class)($video);
        })->reject(function (Video $video): bool {
            return !$video->hasMedia('clips');
        });
    }

    protected function getModels(): LazyCollection
    {
        return Video::with('media')->take(5)->cursor();
    }
}
