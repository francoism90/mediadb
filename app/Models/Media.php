<?php

namespace App\Models;

use App\Traits\InteractsWithHashids;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\ModelStatus\HasStatuses;

class Media extends BaseMedia
{
    use InteractsWithHashids;
    use HasStatuses;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $touches = ['model'];

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getUrl();
    }

    /**
     * @return string
     */
    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    /**
     * @return string
     */
    public function getLocaleAttribute(): string
    {
        return $this->getCustomProperty('locale', 'N/A');
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMissingMetadata(Builder $query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.metadata_collections', ['clip']))
            ->where(function ($query) {
                $query->whereNull('custom_properties->metadata')
                      ->orWhereNull('custom_properties->metadata->duration')
                      ->orWhereNull('custom_properties->metadata->width')
                      ->orWhereNull('custom_properties->metadata->height');
            });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMissingConversions($query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.conversion_collections', ['clip']))
            ->where(function ($query) {
                $query->whereNull('custom_properties->generated_conversions')
                      ->orWhereNull('custom_properties->generated_conversions->sprite')
                      ->orWhereNull('custom_properties->generated_conversions->thumbnail');
            });
    }
}
