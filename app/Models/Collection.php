<?php

namespace App\Models;

use App\Support\Scout\CollectionIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
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

class Collection extends Model implements Viewable
{
    use Activityable;
    use CanBeFavorited;
    use CanBeLiked;
    use Hashidable;
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
    protected $indexConfigurator = CollectionIndexConfigurator::class;

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
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param string|null $field
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->findByHash($value);
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
     * @return mixed
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function tags()
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
     * @return mixed
     */
    public function videos()
    {
        return $this->morphedByMany(
            'App\Models\Video',
            'collectable'
        );
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * @return int
     */
    public function getItemCountAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * @return string
     */
    public function getThumbnailUrlAttribute(): string
    {
        $model = $this->videos()->orderByDesc('created_at')->first();

        return $model ? $model->thumbnail_url : '';
    }
}
