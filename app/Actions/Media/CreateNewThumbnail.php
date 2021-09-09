<?php

namespace App\Actions\Media;

use App\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class CreateNewThumbnail
{
    public function __construct(
        protected CreateVideoFrame $createVideoFrame,
        protected CopyToConversions $copyToConversions,
        protected MarkConversionGenerated $markConversionGenerated
    ) {
    }

    public function execute(Media $media): void
    {
        if ('video' !== $media->type) {
            return;
        }

        $temporaryPath = $this->getTemporaryPath($media);

        $this->createVideoFrame->execute($media, $temporaryPath);

        $this->copyToConversions->execute(
            $media, $temporaryPath, $this->getConversionName($media),
        );

        $this->markConversionGenerated->execute($media, 'thumbnail');
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
