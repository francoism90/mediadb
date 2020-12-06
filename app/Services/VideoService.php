<?php

namespace App\Services;

use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\SetMetadata;
use App\Models\Media;
use App\Models\Video;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\Finder;
use Throwable;

class VideoService
{
    /**
     * @return void
     */
    public function performMaintenance(): void
    {
        $this->deleteExpiredStreamFiles();
        $this->setMissingMetadata();
        $this->hasMissingConversions();
    }

    /**
     * @return void
     */
    protected function setMissingMetadata(): void
    {
        $models = $this->getMissingMetadataModels();

        foreach ($models as $model) {
            SetMetadata::dispatch($model)->onQueue('media');
        }
    }

    /**
     * @return void
     */
    protected function hasMissingConversions(): void
    {
        $models = $this->getMissingConversionModels();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('media');
            }

            if (!$model->hasGeneratedConversion('sprite')) {
                CreateSprite::dispatch($model)->onQueue('media');
            }
        }
    }

    /**
     * @return void
     */
    protected function deleteExpiredStreamFiles(): void
    {
        $files = $this->getExpiredStreamFiles();

        foreach ($files as $file) {
            try {
                unlink($file->getRealPath());
            } catch (Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * @return LazyCollection
     */
    protected function getMissingMetadataModels(): LazyCollection
    {
        return Media::where('model_type', Video::class)
            ->where('collection_name', 'clip')
            ->where(function ($query) {
                $query->whereNull('custom_properties->metadata')
                      ->orWhereNull('custom_properties->metadata->duration')
                      ->orWhereNull('custom_properties->metadata->width')
                      ->orWhereNull('custom_properties->metadata->height');
            })
            ->cursor();
    }

    /**
     * @return LazyCollection
     */
    protected function getMissingConversionModels(): LazyCollection
    {
        return Media::where('model_type', Video::class)
            ->where('collection_name', 'clip')
            ->where(function ($query) {
                $query->whereNull('custom_properties->generated_conversions')
                      ->orWhereNull('custom_properties->generated_conversions->sprite')
                      ->orWhereNull('custom_properties->generated_conversions->thumbnail');
            })
            ->cursor();
    }

    /**
     * @return Finder
     */
    protected function getExpiredStreamFiles(): Finder
    {
        $path = Storage::disk('streams')->path(null);

        return (new Finder())
            ->files()
            ->in($path)
            ->depth(0)
            ->date('until 3 days ago')
            ->name('*.json')
            ->sortByModifiedTime();
    }
}
