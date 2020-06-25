<?php

namespace App\Models;

use App\Support\Scout\MediaIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Resourceable;
use App\Traits\Securable;
use App\Traits\Streamable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Support\Carbon;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
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
    use Resourceable;
    use Searchable;
    use Securable;
    use Sluggable;
    use Streamable;
    use Taggable;
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
        ],
    ];

    /**
     * @return array
     */
    public function sluggable()
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
        return $this->only([
            'id',
            'name',
            'description',
            'model_type',
            'model_id',
        ]);
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
    public function playlists()
    {
        return $this->hasManyJson('App\Models\Playlist', 'custom_properties->media[]->media_id');
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->hasGeneratedConversion('thumbnail')) {
            return '';
        }

        return $this->getTemporaryUrl(
            Carbon::now()->addHours(
                config('vod.expire')
            ), 'thumbnail'
        );
    }

    /**
     * @return string
     */
    public function getPreviewUrlAttribute(): string
    {
        if (!$this->hasGeneratedConversion('preview')) {
            return '';
        }

        return $this->getTemporaryUrl(
            Carbon::now()->addHours(
                config('vod.expire')
            ), 'preview'
        );
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getTemporaryUrl(
            Carbon::now()->addHours(
                config('vod.expire')
            )
        );
    }

    /**
     * @return string
     */
    public function getStreamUrlAttribute(): string
    {
        return self::getSecureExpireLink(
            $this->getStreamUrl(),
            config('vod.secret'),
            config('vod.expire'),
            $this->getRouteKey(),
            request()->ip()
        );
    }

    /**
     * @return string
     */
    public function getPlaceholderUrlAttribute(int $offset = 1000, string $resize = 'w160-h100'): string
    {
        return self::getSecureExpireLink(
            $this->getStreamUrl('thumb', "thumb-{$offset}-{$resize}.jpg"),
            config('vod.secret'),
            config('vod.expire'),
            $this->getRouteKey()."_thumb_{$offset}",
            request()->ip()
        );
    }
}
