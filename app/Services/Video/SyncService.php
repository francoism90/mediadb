<?php

namespace App\Services\Video;

use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Collection;
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

    public function __construct(MetadataService $metadataService)
    {
        $this->metadataService = $metadataService;
    }

    /**
     * @return void
     */
    public function sync(): void
    {
        $this->setMetadata();
        $this->setTags();
        $this->performConversions();
    }

    /**
     * @return void
     */
    protected function setMetadata(): void
    {
        $models = $this->getMediaByMissingMetadata();

        foreach ($models as $model) {
            $this->metadataService->setAttributes($model);
        }
    }

    /**
     * @return void
     */
    protected function setTags(): void
    {
        $collections = $this->getCollectionsWithTags();

        foreach ($collections as $collection) {
            $tags = $collection->tags->all();

            foreach ($collection->videos as $model) {
                $model->attachTags($tags);
            }
        }
    }

    /**
     * @return void
     */
    protected function performConversions(): void
    {
        $models = $this->getMediaByMissingConversions();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('optimize');
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
        return Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->metadata')
            ->orWhereNull('custom_properties->metadata->duration')
            ->orWhereNull('custom_properties->metadata->width')
            ->orWhereNull('custom_properties->metadata->height')
            ->cursor();
    }

    /**
     * @return LazyCollection
     */
    protected function getMediaByMissingConversions(): LazyCollection
    {
        return Media::where('model_type', Video::class)
            ->WhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->cursor();
    }

    /**
     * @return LazyCollection
     */
    protected function getCollectionsWithTags(): LazyCollection
    {
        return Collection::has('tags')
            ->with(['tags', 'videos'])
            ->cursor();
    }
}
