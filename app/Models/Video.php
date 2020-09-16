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
     * @return Collection
     */
    public function getTitlesAttribute()
    {
        return $this
            ->collections()
            ->ofType('title')
            ->get();
    }

    /**
     * @return Collection|MediaCollection
     */
    public function getTracksAttribute()
    {
        return $this->getMedia('tracks');
    }

    /**
     * @return mixed
     */
    public function getBitrateAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.bitrate', 0);
        });
    }

    /**
     * @return mixed
     */
    public function getCodecNameAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.codec_name', 'N/A');
        });
    }

    /**
     * @return mixed
     */
    public function getDurationAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.duration', 0);
        });
    }

    /**
     * @return mixed
     */
    public function getHeightAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.height', 480);
        });
    }

    /**
     * @return mixed
     */
    public function getWidthAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('metadata.width', 768);
        });
    }

    /**
     * @return mixed
     */
    public function getSpriteAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
            return $clip->getCustomProperty('sprite', []);
        });
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function getStreamUrlAttribute()
    {
        return optional($this->getFirstMedia('clip'), function ($clip) {
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
     * @return mixed
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
