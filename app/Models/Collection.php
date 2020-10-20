<?php

namespace App\Models;

use App\Support\Scout\CollectionIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Activityable;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeLiked;
use ScoutElastic\Searchable;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Collection extends Model implements Viewable
{
    use Activityable;
    use CanBeFavorited;
    use CanBeLiked;
    use Hashidable;
    use HasStatuses;
    use InteractsWithViews;
    use Randomable;
    use Searchable;
    use HasTranslations;
    use HasTranslatableSlug;
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
            'overview' => [
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
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->findByHash($value);
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
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'name' => $this->name,
            'overview' => $this->overview,
            'type' => $this->type,
        ];
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
