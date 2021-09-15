<?php

namespace App\Actions\Media;

use App\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class CreateNewThumbnail
{
    public function __invoke(Media $media): void
    {
        if ('video' !== $media->type) {
            return;
        }

        $temporaryDirectory = $this->temporaryDirectory();

        $temporaryPath = $temporaryDirectory->path(
            $this->getConversionName($media)
        );

        app(CreateVideoFrame::class)($media, $temporaryPath);

        app(CopyToConversions::class)(
            $media, $temporaryPath, $this->getConversionName($media),
        );

        app(MarkConversionGenerated::class)($media, 'thumbnail');

        $temporaryDirectory->delete();
    }

    protected function getConversionName(Media $media): string
    {
        return basename($media->getPath('thumbnail'));
    }

    protected function temporaryDirectory(): BaseTemporaryDirectory
    {
        return app(TemporaryDirectory::class)->create();
    }
}
