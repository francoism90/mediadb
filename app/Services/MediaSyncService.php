<?php

namespace App\Services;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class MediaSyncService
{
    /**
     * @var MediaMetadataService
     */
    protected $mediaMetadataService;

    /**
     * @param MediaMetadataService $mediaMetadataService
     */
    public function __construct(MediaMetadataService $mediaMetadataService)
    {
        $this->mediaMetadataService = $mediaMetadataService;
    }

    /**
     * @param bool|null $force
     *
     * @return void
     */
    public function sync(?bool $force = false): void
    {
        $metadataModels = $force ? Media::all() : $this->getModelsByMissingMetadata();
        $conversionModels = $force ? Media::all() : $this->getModelsByMissingConversions();

        $this->performMetadata($metadataModels);
        $this->performConversions($conversionModels, $force);
    }

    /**
     * @param Collection $models
     *
     * @return void
     */
    protected function performMetadata(Collection $models): void
    {
        foreach ($models as $model) {
            $this->mediaMetadataService->setMetadata($model);
        }
    }

    /**
     * @param Collection $models
     * @param bool|null  $force
     *
     * @return void
     */
    protected function performConversions(Collection $models, ?bool $force = false): void
    {
        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail') || $force) {
                CreateThumbnail::dispatch($model)->onQueue('optimize');
            }

            if (!$model->hasGeneratedConversion('preview') || $force) {
                CreatePreview::dispatch($model)->onQueue('optimize');
            }

            if (!$model->hasGeneratedConversion('sprite') || $force) {
                CreateSprite::dispatch($model)->onQueue('optimize');
            }
        }
    }

    /**
     * @return MediaCollection
     */
    protected function getModelsByMissingMetadata(): MediaCollection
    {
        $collection = Media::WhereNull('custom_properties->metadata')
            ->orWhereNull('custom_properties->metadata->duration')
            ->orWhereNull('custom_properties->metadata->width')
            ->orWhereNull('custom_properties->metadata->height')
            ->get();

        return $collection;
    }

    /**
     * @return MediaCollection
     */
    protected function getModelsByMissingConversions(): MediaCollection
    {
        $collection = Media::WhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->preview')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->get();

        return $collection;
    }
}
