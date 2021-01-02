<?php

namespace App\Services;

use App\Models\Media;
use Spatie\MediaLibrary\Support\TemporaryDirectory;
use Spatie\TemporaryDirectory\TemporaryDirectory as BaseTemporaryDirectory;

class MediaConversionService
{
    /**
     * @var MediaImportService
     */
    protected MediaImportService $mediaImportService;

    public function __construct(MediaImportService $mediaImportService)
    {
        $this->mediaImportService = $mediaImportService;
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
    public function importConversion(Media $media, string $path, string $name): self
    {
        $this->mediaImportService->copyToConversions($media, $path, $name);

        unlink($path);

        return $this;
    }
}
