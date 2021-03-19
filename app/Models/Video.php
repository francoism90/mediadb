<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use CanBeLiked;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;
    use Searchable;

    /**
     * @var array
     */
    public array $translatable = ['name', 'slug', 'overview'];

    /**
     * @var array
     */
    protected $with = ['media'];

    /**
     * @return morphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => array_values($this->getTranslations('name')),
            'overview' => array_values($this->getTranslations('overview')),
            'type' => $this->type,
            'status' => $this->status,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'tags' => $this->extractTagTranslations(),
        ];
    }

    /**
     * We need to register media conversion to use them.
     * Services do the actual conversion.
     *
     * @param Media $media
     */
    public function registerMediaConversions($media = null): void
    {
        $serviceConversions = [
            'sprite',
            'thumbnail',
        ];

        foreach ($serviceConversions as $conversion) {
            $this->addMediaConversion($conversion)
                 ->withoutManipulations()
                 ->performOnCollections('service-collection')
                 ->nonQueued();
        }
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('clip')
            ->acceptsMimeTypes(
                config('video.accept_mimetypes')
            )
            ->singleFile()
            ->useDisk('media');

        $this
            ->addMediaCollection('caption')
            ->acceptsMimeTypes([
                'text/plain',
                'text/vtt',
            ])
            ->useDisk('media');
    }

    /**
     * @return MediaCollection
     */
    public function getTracksAttribute(): MediaCollection
    {
        return $this->getMedia('caption');
    }

    /**
     * @return int|null
     */
    public function getBitrateAttribute(): ?int
    {
        return optional(
            $this->getFirstMedia('clip'),
            fn ($clip) => $clip->getCustomProperty('metadata.bitrate', 0)
        );
    }

    /**
     * @return string|null
     */
    public function getCodecNameAttribute(): ?string
    {
        return optional(
            $this->getFirstMedia('clip'),
            fn ($clip) => $clip->getCustomProperty('metadata.codec_name', 'N/A')
        );
    }

    /**
     * @return float|null
     */
    public function getDurationAttribute(): ?float
    {
        return optional(
            $this->getFirstMedia('clip'),
            fn ($clip) => $clip->getCustomProperty('metadata.duration', 0)
        );
    }

    /**
     * @return int|null
     */
    public function getHeightAttribute(): ?int
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.height', 360);
        });
    }

    /**
     * @return int|null
     */
    public function getWidthAttribute(): ?int
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.width', 480);
        });
    }

    /**
     * @return string|null
     */
    public function getResolutionAttribute(): ?string
    {
        $resolutions = collect(
            config('video.resolutions', [])
        );

        $videoWidth = $this->width ?? 480;

        $resolution = $resolutions
            ->whereBetween('width', [$videoWidth - 128, $videoWidth + 128])
            ->last();

        return $resolution['label'] ?? '240p';
    }

    /**
     * @return array|null
     */
    public function getSpriteAttribute(): ?array
    {
        return optional(
            $this->getFirstMedia('clip'),
            fn ($clip) => $clip->getCustomProperty('sprite', [])
        );
    }

    /**
     * @return string|null
     */
    public function getSpriteUrlAttribute(): ?string
    {
        return optional($this->getFirstMedia('clip'), fn ($clip) => URL::signedRoute(
            'api.media.asset',
            [
                'media' => $clip,
                'user' => auth()->user(),
                'name' => 'sprite',
                'version' => $clip->updated_at->timestamp,
            ]
        ));
    }

    /**
     * @return string|null
     */
    public function getStreamUrlAttribute(): ?string
    {
        return optional($this->getFirstMedia('clip'), fn ($clip) => URL::signedRoute(
            'api.vod.stream',
            [
                'user' => auth()->user(),
                'video' => $this,
            ]
        ));
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return optional($this->getFirstMedia('clip'), fn ($clip) => URL::signedRoute(
            'api.media.asset',
            [
                'media' => $clip,
                'user' => auth()->user(),
                'name' => 'thumbnail',
                'version' => $clip->updated_at->timestamp,
            ]
        ));
    }
}
