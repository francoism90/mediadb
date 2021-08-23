<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $touches = ['model'];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    public function getLocaleAttribute(): ?string
    {
        return $this->getCustomProperty('locale');
    }

    public function getBitrateAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.bitrate');
    }

    public function getCodecNameAttribute(): ?string
    {
        return $this->getCustomProperty('metadata.codec_name');
    }

    public function getDurationAttribute(): ?float
    {
        return $this->getCustomProperty('metadata.duration');
    }

    public function getHeightAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.height');
    }

    public function getWidthAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.width');
    }

    public function getThumbnailAttribute(): ?float
    {
        return $this->getCustomProperty('thumbnail');
    }

    public function getResolutionAttribute(): ?array
    {
        $mediaWidth = $this->width ?? 480;

        return collect(config('api.resolutions'))
            ->whereBetween('width', [$mediaWidth - 128, $mediaWidth + 128])
            ->last();
    }

    public function scopeMissingMetadata(Builder $query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.collections', ['clip']))
            ->where(function ($query): void {
                $query->whereNull('custom_properties->metadata')
                      ->orWhereNull('custom_properties->metadata->duration')
                      ->orWhereNull('custom_properties->metadata->height')
                      ->orWhereNull('custom_properties->metadata->width');
            });
    }

    public function scopeMissingConversions(Builder $query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.collections', ['clip']))
            ->where(function ($query): void {
                $query->whereNull('generated_conversions')
                      ->orWhereNull('generated_conversions->thumbnail');
            });
    }
}
