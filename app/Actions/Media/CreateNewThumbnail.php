<?php

namespace App\Actions\Media;

use App\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class CreateNewThumbnail
{
    public function execute(Media $media): void
    {
        if ('video' !== $media->type) {
            return;
        }

        $temporaryPath = $this->getTemporaryPath($media);

        app(CreateVideoFrame::class)->execute($media, $temporaryPath);

        app(CopyToConversions::class)->execute(
            $media, $temporaryPath, $this->getConversionName($media),
        );

        app(MarkConversionGenerated::class)->execute($media, 'thumbnail');
    }

    protected function getConversionName(Media $media): string
    {
        return basename($media->getPath('thumbnail'));
    }

    protected function getTemporaryPath(Media $media): string
    {
        return $this->temporaryDirectory()->path(
             $this->getConversionName($media)
        );
    }

    protected function temporaryDirectory(): BaseTemporaryDirectory
    {
        return app(TemporaryDirectory::class)->create();
    }
}
