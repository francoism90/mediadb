<?php

namespace App\Models;

use App\Support\Scout\Rules\MultiMatchRule;
use App\Support\Scout\VideoIndexConfigurator;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Taggable;
use App\Traits\Translatable;
use App\Traits\Viewable as ViewableHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
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
    use HasTranslations;
    use HasTranslatableSlug;
    use Activityable;
    use CanBeLiked;
    use CanBeFavorited;
    use Hashidable;
    use HasStatuses;
    use InteractsWithMedia;
    use InteractsWithViews;
    use Notifiable;
    use Randomable;
    use Searchable;
    use Translatable;
    use ViewableHelpers;
    use HasTags, Taggable {
        Taggable::getTagClassName insteadof HasTags;
        Taggable::tags insteadof HasTags;
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
     * @param mixed       $value
     * @param string|null $field
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    // public function resolveRouteBinding($value, $field = null)
    // {
    //     return $this->findByHash($value);
    // }

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
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
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
     * @return mixed
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function collections()
    {
        return $this->morphToMany(
            'App\Models\Collection',
            'collectable'
        );
    }

    /**
     * @return mixed
     */
    public function getTitlesAttribute()
    {
        return $this
            ->collections()
            ->ofType('title')
            ->get();
    }

    /**
     * @return mixed
     */
    public function getTracksAttribute()
    {
        return $this->getMedia('tracks');
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
