<?php

namespace App\Services;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;
use App\Models\Video;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class VideoSyncService
{
    /**
     * @var VideoMetadataService
     */
    protected $videoMetadataService;

    public function __construct(
        VideoMetadataService $videoMetadataService
    ) {
        $this->videoMetadataService = $videoMetadataService;
    }

    /**
     * @param bool|null $force
     *
     * @return void
     */
    public function sync(bool $force = false): void
    {
        $metadataModels = $this->getMediaByMissingMetadata($force);
        $conversionModels = $this->getMediaByMissingConversions($force);

        $this->setMetadata($metadataModels);
        $this->performConversions($conversionModels, $force);
    }

    /**
     * @param Collection $models
     *
     * @return void
     */
    protected function setMetadata(Collection $models): void
    {
        foreach ($models as $model) {
            $this->videoMetadataService->setAttributes($model);
        }
    }

    /**
     * @param Collection $models
     * @param bool|null  $force
     *
     * @return void
     */
    protected function performConversions(Collection $models): void
    {
        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('optimize');
            }

            if (!$model->hasGeneratedConversion('preview')) {
                CreatePreview::dispatch($model)->onQueue('optimize');
            }

            if (!$model->hasGeneratedConversion('sprite')) {
                CreateSprite::dispatch($model)->onQueue('optimize');
            }
        }
    }

    /**
     * @return MediaCollection
     */
    protected function getMediaByMissingMetadata(): MediaCollection
    {
        $collection = Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->metadata')
            ->orWhereNull('custom_properties->metadata->duration')
            ->orWhereNull('custom_properties->metadata->width')
            ->orWhereNull('custom_properties->metadata->height')
            ->get();

        return $collection;
    }

    /**
     * @return MediaCollection
     */
    protected function getMediaByMissingConversions(): MediaCollection
    {
        $collection = Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->preview')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->get();

        return $collection;
    }
}
