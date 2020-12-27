<?php

namespace App\Models;

use App\Traits\HasCollections;
use App\Traits\InteractsWithAcquaintances;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\URL;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Video extends BaseModel
{
    use CanBeFavorited;
    use CanBeLiked;
    use HasCollections;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;

    /**
     * @var array
     */
    public $translatable = ['name', 'slug', 'overview'];

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
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'duration' => $this->duration,
        ];
    }

    /**
     * @return \Illuminate\Support\Collection|null
     */
    public function getTracksAttribute()
    {
        return $this->getMedia('caption');
    }

    /**
     * @return int|null
     */
    public function getBitrateAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.bitrate', 0);
        });
    }

    /**
     * @return string|null
     */
    public function getCodecNameAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.codec_name', 'N/A');
        });
    }

    /**
     * @return int|null
     */
    public function getDurationAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.duration', 0);
        });
    }

    /**
     * @return int|null
     */
    public function getHeightAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.height', 480);
        });
    }

    /**
     * @return int|null
     */
    public function getWidthAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.width', 768);
        });
    }

    /**
     * @return array|null
     */
    public function getSpriteAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('sprite', []);
        });
    }

    /**
     * @return string|null
     */
    public function getSpriteUrlAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return URL::signedRoute(
                'api.media.asset',
                [
                    'media' => $clip,
                    'user' => auth()->user(),
                    'name' => 'sprite',
                    'version' => $clip->updated_at->timestamp,
                ]
            );
        });
    }

    /**
     * @return string|null
     */
    public function getStreamUrlAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return URL::signedRoute(
                'api.vod.stream',
                [
                    'video' => $this,
                    'user' => auth()->user(),
                ]
            );
        });
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return URL::signedRoute(
                'api.media.asset',
                [
                    'media' => $clip,
                    'user' => auth()->user(),
                    'name' => 'thumbnail',
                    'version' => $clip->updated_at->timestamp,
                ]
            );
        });
    }
}
