<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\MediaLibraryService;

class CopyToConversions
{
    public function __construct(
        protected MediaLibraryService $mediaLibraryService,
    ) {
    }

    public function execute(Media $media, string $path, string $name): void
    {
        $this->mediaLibraryService->copyToMediaLibrary(
            $path,
            $media,
            'conversions',
            $name,
        );
    }
}
