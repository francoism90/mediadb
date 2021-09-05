<?php

namespace App\Services;

use App\Events\Media\HasBeenAdded;
use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\SetMetadata;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class SyncService
{
    public function __construct(
        protected Finder $finder,
        protected Filesystem $filesystem,
    ) {
    }

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

    public function gatherFiles(string $path): Finder
    {
        return $this->finder->create()
            ->files()
            ->followLinks()
            ->sortByName()
            ->name(config('api.sync.extensions'))
            ->in($path);
    }

    public static function handleMissingMetadata(): void
    {
        $models = Media::missingMetadata()->cursor();

        foreach ($models as $model) {
            SetMetadata::dispatch($model)->onQueue('media');
        }
    }

    public static function handleMissingConversions(): void
    {
        $models = Media::missingConversions()->cursor();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('media');
            }
        }
    }
}
