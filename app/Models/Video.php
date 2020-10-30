<?php

namespace App\Models;

use App\Support\Scout\Rules\MultiMatchRule;
use App\Support\Scout\VideoIndexConfigurator;
use App\Traits\HasAcquaintances;
use App\Traits\HasActivities;
use App\Traits\HasCollections;
use App\Traits\HasHashids;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithTags;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Video extends Model implements HasMedia, Viewable
{
    use HasActivities;
    use HasCollections;
    use HasHashids;
    use HasRandomSeed;
    use HasStatuses;
    use HasTranslatableSlug;
    use HasTranslations;
    use HasViews;
    use InteractsWithMedia;
    use InteractsWithViews;
    use Notifiable;
    use Searchable;
    use CanBeFavorited;
    use HasAcquaintances;
    use HasTags, InteractsWithTags {
        InteractsWithTags::getTagClassName insteadof HasTags;
        InteractsWithTags::tags insteadof HasTags;
    }

    /**
     * @var array
     */
    public $translatable = ['name', 'slug', 'overview'];

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
        MultiMatchRule::class,
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
            'overview' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
            'duration' => [
                'type' => 'float',
            ],
            'file_name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
        ],
    ];

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
        $this->addMediaCollection('clip')
             ->singleFile()
             ->useDisk('media');

        $this->addMediaCollection('tracks')
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
            'type' => $this->type,
            'status' => $this->status,
            'release_date' => $this->release_date,
            'file_name' => $this->file_name,
            'duration' => $this->duration,
            'season_number' => $this->season_number,
            'episode_number' => $this->episode_number,
            'overview' => $this->overview,
        ];
    }

    /**
     * @return int|null
     */
    public function getBitrateAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('metadata.bitrate', 0);
        });
    }

    /**
     * @return string|null
     */
    public function getCodecNameAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('metadata.codec_name', 'N/A');
        });
    }

    /**
     * @return int|null
     */
    public function getDurationAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('metadata.duration', 0);
        });
    }

    /**
     * @return int|null
     */
    public function getHeightAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('metadata.height', 480);
        });
    }

    /**
     * @return int|null
     */
    public function getWidthAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('metadata.width', 768);
        });
    }

    /**
     * @return array|null
     */
    public function getSpriteAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return $clip->getCustomProperty('sprite', []);
        });
    }

    /**
     * @return string|null
     */
    public function getSpriteUrlAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
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
        return optional($this->getFirstMedia('clips'), function ($clip) {
            return URL::signedRoute(
                'api.media.stream',
                [
                    'media' => $clip,
                    'user' => auth()->user(),
                ]
            );
        });
    }

    /**
     * @return Media|null
     */
    public function getTracksAttribute()
    {
        return $this->getMedia('tracks');
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return optional($this->getFirstMedia('clips'), function ($clip) {
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
