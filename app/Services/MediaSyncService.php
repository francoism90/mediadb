<?php

namespace App\Services;

use App\Events\Media\HasBeenAdded;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Log\Logger;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class MediaSyncService
{
    public function __construct(
        protected Finder $finder,
        protected Filesystem $filesystem,
        protected Logger $logger
    ) {
    }

    /**
     * @param Model       $model
     * @param SplFileInfo $file
     * @param string      $collection
     * @param array       $properties
     *
     * @return void
     */
    public function add(
        Model $model,
        SplFileInfo $file,
        string $collection = null,
        array $properties = []
    ): void {
        $path = $file->getRealPath();
        $extension = $file->getExtension();

        $media = $model
                ->addMedia($path)
                ->withCustomProperties($properties)
                ->storingConversionsOnDisk('conversions')
                ->toMediaCollection($collection);

        // Force WebVTT
        if ('vtt' === $extension) {
            $media->mime_type = 'text/vtt';
            $media->save();
        }

        event(new HasBeenAdded($model, $media));
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    public function gatherFiles(string $path): Finder
    {
        return $this->finder->create()
            ->files()
            ->followLinks()
            ->name(config('media.importable'))
            ->in($path);
    }
}
