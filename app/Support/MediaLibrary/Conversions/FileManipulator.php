<?php

namespace App\Support\MediaLibrary\Conversions;

use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Conversions\Actions\PerformConversionAction;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Conversions\ConversionCollection;
use Spatie\MediaLibrary\Conversions\ImageGenerators\ImageGeneratorFactory;
use Spatie\MediaLibrary\Conversions\Jobs\PerformConversionsJob;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class FileManipulator
{
    public function createDerivedFiles(
        Media $media,
        array $onlyConversionNames = [],
        bool $onlyMissing = false
    ): void {
        if (!$this->canConvertMedia($media)) {
            return;
        }

        [$queuedConversions, $conversions] = ConversionCollection::createForMedia($media)
            ->filter(function (Conversion $conversion) use ($onlyConversionNames) {
                if (0 === count($onlyConversionNames)) {
                    return true;
                }

                return in_array($conversion->getName(), $onlyConversionNames);
            })
            ->filter(fn (Conversion $conversion) => $conversion->shouldBePerformedOn($media->collection_name))
            ->partition(fn (Conversion $conversion) => $conversion->shouldBeQueued());

        $this
            ->performConversions($conversions, $media, $onlyMissing)
            ->dispatchQueuedConversions($media, $queuedConversions, $onlyMissing);
    }

    public function performConversions(
        ConversionCollection $conversions,
        Media $media,
        bool $onlyMissing = false
    ): self {
        if ($conversions->isEmpty()) {
            return $this;
        }

        $conversions
            ->reject(function (Conversion $conversion) use ($onlyMissing, $media) {
                $relativePath = $media->getPath($conversion->getName());

                if ($rootPath = config("filesystems.disks.{$media->disk}.root")) {
                    $relativePath = str_replace($rootPath, '', $relativePath);
                }

                return $onlyMissing && Storage::disk($media->disk)->exists($relativePath);
            })
            ->each(function (Conversion $conversion) use ($media) {
                (new PerformConversionAction())->execute($conversion, $media, $media->getPath());
            });

        return $this;
    }

    protected function dispatchQueuedConversions(
        Media $media,
        ConversionCollection $conversions,
        bool $onlyMissing = false
    ): self {
        if ($conversions->isEmpty()) {
            return $this;
        }

        $performConversionsJobClass = config(
            'media-library.jobs.perform_conversions',
            PerformConversionsJob::class
        );

        /** @var PerformConversionsJob $job */
        $job = (new $performConversionsJobClass($conversions, $media, $onlyMissing))
            ->onQueue(config('media-library.queue_name'));

        dispatch($job);

        return $this;
    }

    protected function canConvertMedia(Media $media): bool
    {
        $imageGenerator = ImageGeneratorFactory::forMedia($media);

        return $imageGenerator ? true : false;
    }
}
