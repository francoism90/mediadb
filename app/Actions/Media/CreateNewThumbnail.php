<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\MediaLibraryService;

class CreateNewThumbnail
{
    public function __construct(
        protected MediaLibraryService $mediaLibraryService,
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

        $path = $this->getTemporaryPath();

        $this->createVideoFrame->execute($media, $path);

        $this->copyToConversions->execute(
            $media, $path, config('api.conversions.thumbnail.path')
        );

        $this->MarkConversionGenerated->execute($media, 'thumbnail');
    }

    private function getTemporaryPath(): string
    {
        return $this->mediaLibraryService->temporaryPath(
            config('api.conversions.thumbnail.path')
        );
    }
}
