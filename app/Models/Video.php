<?php

namespace App\Models;

use App\Support\Scout\Rules\SimpleMatchRule;
use App\Support\Scout\VideoIndexConfigurator;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;

class Video extends Model implements HasMedia, Viewable
{
    use Activityable;
    use CanBeLiked;
    use CanBeFavorited;
    use Hashidable;
    use HasStatuses;
    use HasTags;
    use InteractsWithMedia;
    use InteractsWithViews;
    use Notifiable;
    use Randomable;
    use Searchable;
    use Sluggable;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var string
     */
    protected $indexConfigurator = VideoIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        SimpleMatchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
            'file_name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
            'duration' => [
                'type' => 'float',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'overview' => $this->overview,
            'type' => $this->type,
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'file_name' => $this->file_name,
            'duration' => $this->duration,
        ];
    }

    /**
     * @return MorphTo
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
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
            'preview',
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
        $this->addMediaCollection('clip')
             ->singleFile()
             ->useDisk('media');

        $this->addMediaCollection('tracks')
             ->useDisk('media');
    }

    /**
     * @return string
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this
            ->morphToMany(
                self::getTagClassName(),
                'taggable',
                'taggables',
                null,
                'tag_id'
            )
            ->orderBy('order_column');
    }

    /**
     * @return MorphToMany
     */
    public function collections(): MorphToMany
    {
        return $this->morphToMany(
            'App\Models\Collection',
            'collectable'
        );
    }

    /**
     * @return Media
     */
    public function getFirstClip(): ?Media
    {
        return $this->getMedia('clip')->first();
    }

    /**
     * @return array
     */
    public function getMetadataAttribute(): array
    {
        $clip = $this->getFirstClip();

        if (!$clip) {
            return [];
        }

        return [
            'file_name' => $clip->file_name,
            'probe_score' => $clip->getCustomProperty('metadata.probe_score', 0),
            'aspect_ratio' => $clip->getCustomProperty('metadata.display_aspect_ratio', 'N/A'),
            'bitrate' => $clip->getCustomProperty('metadata.bitrate', 0),
            'codec_name' => $clip->getCustomProperty('metadata.codec_name', 'N/A'),
            'duration' => $clip->getCustomProperty('metadata.duration', 0),
            'size' => $clip->getCustomProperty('metadata.size', 0),
            'height' => $clip->getCustomProperty('metadata.height', 0),
            'width' => $clip->getCustomProperty('metadata.width', 0),
        ];
    }

    /**
     * @return int
     */
    public function getDurationAttribute(): int
    {
        $metadata = $this->getMetadataAttribute();

        return $metadata['duration'] ?? 0;
    }

    /**
     * @return Collection|MediaCollection
     */
    public function getTracksAttribute()
    {
        return $this->getMedia('tracks');
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        $firstClip = $this->getFirstClip();

        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $firstClip,
                'user' => auth()->user(),
                'name' => 'thumbnail.jpg',
                'version' => $firstClip->updated_at->timestamp,
            ]
        );
    }

    /**
     * @return string
     */
    public function getPreviewUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.preview',
            [
                'media' => $this->getFirstClip(),
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
                'media' => $this->getFirstClip(),
                'user' => auth()->user(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getStreamUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.stream',
            [
                'media' => $this->getFirstClip(),
                'user' => auth()->user(),
            ]
        );
    }
}
