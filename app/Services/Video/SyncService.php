<?php

namespace App\Services\Video;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;
use App\Models\Video;
use App\Services\Media\MetadataService;
use Illuminate\Support\LazyCollection;

class SyncService
{
    /**
     * @var MetadataService
     */
    protected $metadataService;

    public function __construct(
        MetadataService $metadataService
    ) {
        $this->metadataService = $metadataService;
    }

    /**
     * @return void
     */
    public function sync(): void
    {
        $metadataModels = $this->getMediaByMissingMetadata();
        $conversionModels = $this->getMediaByMissingConversions();

        $this->setMetadata($metadataModels);
        $this->performConversions($conversionModels);
    }

    /**
     * @param LazyCollection $models
     *
     * @return void
     */
    protected function setMetadata(LazyCollection $models): void
    {
        foreach ($models as $model) {
            $this->metadataService->setAttributes($model);
        }
    }

    /**
     * @param LazyCollection $models
     *
     * @return void
     */
    protected function performConversions(LazyCollection $models): void
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
     * @return LazyCollection
     */
    protected function getMediaByMissingMetadata(): LazyCollection
    {
        $collection = Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->metadata')
            ->orWhereNull('custom_properties->metadata->duration')
            ->orWhereNull('custom_properties->metadata->width')
            ->orWhereNull('custom_properties->metadata->height')
            ->cursor();

        return $collection;
    }

    /**
     * @return LazyCollection
     */
    protected function getMediaByMissingConversions(): LazyCollection
    {
        $collection = Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->preview')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->cursor();

        return $collection;
    }
}
