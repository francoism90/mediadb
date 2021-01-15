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
    public function scopeMissingConversions(Builder $query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.conversion_collections', ['clip']))
            ->where(function ($query) {
                $query->whereNull('custom_properties->generated_conversions')
                      ->orWhereNull('custom_properties->generated_conversions->sprite')
                      ->orWhereNull('custom_properties->generated_conversions->thumbnail');
            });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithDuration(Builder $query, int $min = 0, int $max = 40): Builder
    {
        $durations = collect(
            config('video.filter_durations', [])
        );

        // Skip query on full ranges
        if ($min === $durations->first() && $max === $durations->last()) {
            return $query;
        }

        $min = $min === $max ? $min - 10 : $min;
        $max = $durations->last() === $max ? $max * 24 : $max;

        return $query
            ->whereIn('collection_name', config('media.conversion_collections', ['clip']))
            ->where(function ($query) use ($min, $max) {
                $query->whereBetween('custom_properties->metadata->duration', [
                    $min * 60, // time in secs
                    $max * 60,
                ]);
            });
    }
}
