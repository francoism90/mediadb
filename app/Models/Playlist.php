<?php

namespace App\Models;

use App\Support\Scout\PlaylistIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Resourceable;
use App\Traits\Securable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use ScoutElastic\Searchable;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Playlist extends Model implements Viewable
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
    use Sluggable;
    use Securable;
    use Taggable;
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
    protected $indexConfigurator = PlaylistIndexConfigurator::class;

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
     * @return void
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
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
     * @return belongsToJson
     */
    public function media()
    {
        return $this->belongsToJson('App\Models\Media', 'custom_properties->media[]->media_id');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        return '';
    }
}
