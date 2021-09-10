<?php

namespace App\Actions\Media;

use App\Events\Media\MediaHasBeenAdded;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\Finder\SplFileInfo;

class ImportToMediaLibrary
{
    public function execute(
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

        event(new MediaHasBeenAdded($model, $media));
    }
}
