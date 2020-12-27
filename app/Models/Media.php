<?php

namespace App\Models;

use App\Traits\InteractsWithHashids;
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
    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    /**
     * @return string
     */
    public function getUrlAttribute(?string $conversion = null): string
    {
        return $this->getUrl($conversion);
    }

    /**
     * @return string
     */
    public function getLocaleAttribute(): string
    {
        return $this->getCustomProperty('locale', 'N/A');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMissingMetadata($query)
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeMissingConversions($query)
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
