<?php

namespace App\Services;

use App\Models\Media;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class MediaConversionService
{
    public function __construct(
        protected Filesystem $filesystem,
    ) {
    }

    /**
     * @return BaseTemporaryDirectory
     */
    public function temporaryDirectory(): BaseTemporaryDirectory
    {
        return (new TemporaryDirectory())->create();
    }

    /**
     * @param Media  $media
     * @param string $path
     * @param string $name
     *
     * @return self
     */
    public function import(Media $media, string $path, string $name): self
    {
        $this->filesystem->copyToMediaLibrary(
            $path,
            $media,
            'conversions',
            $name
        );

        unlink($path);

        return $this;
    }
}
