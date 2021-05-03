<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\ModelStatus\HasStatuses;

class Media extends BaseMedia
{
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
     * @var array
     */
    protected $appends = ['thumbnail_url'];

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

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
    public function getLocaleAttribute(): ?string
    {
        return $this->getCustomProperty('locale');
    }

    /**
     * @return int|null
     */
    public function getBitrateAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.bitrate');
    }

    /**
     * @return string|null
     */
    public function getCodecNameAttribute(): ?string
    {
        return $this->getCustomProperty('metadata.codec_name');
    }

    /**
     * @return float|null
     */
    public function getDurationAttribute(): ?float
    {
        return $this->getCustomProperty('metadata.duration');
    }

    /**
     * @return int|null
     */
    public function getHeightAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.height');
    }

    /**
     * @return int|null
     */
    public function getWidthAttribute(): ?int
    {
        return $this->getCustomProperty('metadata.width');
    }

    /**
     * @return float|null
     */
    public function getThumbnailAttribute(): ?float
    {
        return $this->getCustomProperty('thumbnail');
    }

    /**
     * @return string|null
     */
    public function getResolutionAttribute(): ?string
    {
        $resolutions = collect(
            config('media.resolutions', [])
        );

        $mediaWidth = $this->width ?? 480;

        $resolution = $resolutions
            ->whereBetween('width', [$mediaWidth - 128, $mediaWidth + 128])
            ->last();

        return $resolution['label'] ?? null;
    }

    /**
     * @return string
     */
    public function getStreamUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.stream',
            [
                'media' => $this,
                'user' => auth()->user(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getSpriteUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.sprite',
            [
                'media' => $this,
                'user' => auth()->user(),
            ]
        );
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this,
                'user' => auth()->user(),
                'name' => 'thumbnail',
                'version' => $this->updated_at->timestamp,
            ]
        );
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeMissingMetadata(Builder $query): Builder
    {
        return $query
            ->whereIn('collection_name', config('media.collections', ['clip']))
            ->where(function ($query) {
                $query->whereNull('custom_properties->metadata')
                      ->orWhereNull('custom_properties->metadata->duration')
                      ->orWhereNull('custom_properties->metadata->height')
                      ->orWhereNull('custom_properties->metadata->width');
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
            ->whereIn('collection_name', config('media.collections', ['clip']))
            ->where(function ($query) {
                $query->whereNull('generated_conversions')
                      ->orWhereNull('generated_conversions->thumbnail');
            });
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeWithDurations(Builder $query, int $min = 0, int $max = 40): Builder
    {
        $durations = collect(
            config('media.filter_durations', [])
        );

        // Skip query on full ranges
        if ($min === $durations->first() && $max === $durations->last()) {
            return $query;
        }

        $min = $min === $max ? $min - 10 : $min;
        $max = $durations->last() === $max ? $max * 24 : $max;

        return $query
            ->whereIn('media.collection_name', config('media.duration_collections', ['clip']))
            ->where(function ($query) use ($min, $max) {
                $query->whereBetween('custom_properties->metadata->duration', [
                    $min * 60, // time in secs
                    $max * 60,
                ]);
            });
    }
}
