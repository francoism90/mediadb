<?php

namespace App\Models;

use App\Support\Scout\MediaIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Support\Facades\URL;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Media extends BaseMedia implements Viewable
{
    use Activityable;
    use CanBeFavorited;
    use CanBeLiked;
    use Hashidable;
    use HasJsonRelationships;
    use HasStatuses;
    use HasTags;
    use InteractsWithViews;
    use Randomable;
    use Searchable;
    use Sluggable;
    use ViewableHelpers;

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
    protected $indexConfigurator = MediaIndexConfigurator::class;

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
            'description' => [
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
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'duration' => $this->getCustomProperty('metadata.duration', 0),
        ];
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
    public function tags()
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }

    /**
     * @return hasManyJson
     */
    public function collections()
    {
        return $this->hasManyJson('App\Models\Collections', 'custom_properties->media[]->media_id');
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBasePath();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBaseMediaPath();
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        $expires = now()->addSeconds(
            config('vod.expire')
        );

        return $this->getTemporaryUrl($expires);
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this,
                'user' => auth()->user(),
                'name' => 'thumbnail.jpg',
            ]
        );
    }

    /**
     * @return string
     */
    public function getPreviewUrlAttribute(): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this,
                'user' => auth()->user(),
                'name' => 'preview.webm',
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
}
