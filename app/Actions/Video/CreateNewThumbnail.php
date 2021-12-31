<?php

namespace App\Actions\Video;

use App\Actions\Media\CreateMediaThumbnail;
use App\Models\Video;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class CreateNewThumbnail
{
    public function __invoke(Video $video): void
    {
        // Setup temporary directory
        $temporaryDirectory = $this->temporaryDirectory();

        $temporaryPath = $temporaryDirectory->path(
            $this->getConversionName()
        );

        // Create frame of the best resolution clip
        app(CreateMediaThumbnail::class)($video->clip, $temporaryPath);

        // Import to media-library
        $video
            ->addMedia($temporaryPath)
            ->storingConversionsOnDisk('conversions')
            ->usingName($video->name)
            ->toMediaCollection('thumbnail');

        // Delete leftovers
        $temporaryDirectory->delete();
    }

    protected function getConversionName(): string
    {
        return 'thumbnail.jpg';
    }

    protected function temporaryDirectory(): BaseTemporaryDirectory
    {
        return app(TemporaryDirectory::class)->create();
    }
}
