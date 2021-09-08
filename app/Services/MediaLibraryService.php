<?php

namespace App\Services;

use App\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class MediaLibraryService
{
    public function __construct(
        protected Filesystem $filesystem,
    ) {
    }

    public function copyToMediaLibrary(
        string $path,
        Media $media,
        ?string $type = null,
        ?string $name = null
    ): void {
        $this->filesystem->copyToMediaLibrary(
            $path,
            $media,
            $type,
            $name
        );
    }

    public function temporaryPath(string $path): string
    {
        return $this->temporaryDirectory->path($path);
    }

    public function temporaryDirectory(): BaseTemporaryDirectory
    {
        return (new TemporaryDirectory())->create();
    }
}
