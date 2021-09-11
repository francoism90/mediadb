<?php

namespace App\Actions\Media;

use App\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Filesystem;

class CopyToConversions
{
    public function __invoke(Media $media, string $path, string $name): void
    {
        app(Filesystem::class)->copyToMediaLibrary(
            $path,
            $media,
            'conversions',
            $name,
        );
    }
}
