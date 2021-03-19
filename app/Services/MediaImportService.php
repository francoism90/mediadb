<?php

namespace App\Services;

use App\Events\Media\HasBeenAdded;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class MediaImportService
{
    /**
     * @var Filesystem
     */
    protected Filesystem $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Model       $baseModel
     * @param SplFileInfo $file
     * @param string      $collection
     * @param array       $properties
     *
     * @return void
     */
    public function import(
        Model $baseModel,
        SplFileInfo $file,
        string $collection = null,
        array $properties = []
    ): void {
        try {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            $media = $baseModel
                    ->addMedia($filePath)
                    ->withCustomProperties($properties)
                    ->storingConversionsOnDisk('conversions')
                    ->toMediaCollection($collection);

            // Force WebVTT
            if ('vtt' === $fileExtension) {
                $media->mime_type = 'text/vtt';
                $media->save();
            }

            event(new HasBeenAdded($baseModel, $media));
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @param Media  $media
     * @param string $path
     * @param string $name
     *
     * @return void
     */
    public function copyToConversions(Media $media, string $path, string $name): void
    {
        $this->filesystem->copyToMediaLibrary(
            $path,
            $media,
            'conversions',
            $name
        );
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    public function getValidFiles(string $path): Finder
    {
        $filter = fn (SplFileInfo $file) => $file->isReadable() && $file->isWritable();

        return (new Finder())
            ->files()
            ->in($path)
            ->depth('== 0')
            ->filter($filter)
            ->sortByName();
    }
}
