<?php

namespace App\Models;

use App\Support\Scout\CollectionIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\HasAcquaintances;
use App\Traits\HasActivities;
use App\Traits\HasHashids;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithTags;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Model;
use Multicaret\Acquaintances\Traits\CanBeSubscribed;
use ScoutElastic\Searchable;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Collection extends Model implements Viewable
{
    use HasActivities;
    use HasHashids;
    use HasRandomSeed;
    use HasStatuses;
    use HasTranslatableSlug;
    use HasTranslations;
    use HasViews;
    use InteractsWithViews;
    use Searchable;
    use CanBeSubscribed;
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
            'name' => $this->name,
            'type' => $this->type,
            'overview' => $this->overview,
        ];
    }

    /**
     * @return morphedByMany
     */
    public function videos()
    {
        return $this
            ->morphedByMany(Video::class, 'collectable');
    }

    /**
     * @param string|array|\ArrayAccess $values
     * @param string|null               $type
     * @param string|null               $locale
     *
     * @return Collection|static
     */
    public static function findOrCreate($values, string $type = null, string $locale = null)
    {
        $collection = collect($values)->map(function ($value) use ($type, $locale) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value, $type, $locale);
        });

        return is_string($values) ? $collection->first() : $collection;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $locale
     *
     * @return Collection|static
     */
    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        $collection = static::findFromString($name, $type, $locale);

        if (!$collection) {
            $collection = static::create([
                'name' => [$locale => $name],
                'type' => $type,
            ]);
        }

        return $collection;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $locale
     *
     * @return Collection|null
     */
    public static function findFromString(string $name, string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->where('type', $type)
            ->first();
    }

    /**
     * @param string $name
     * @param string $locale
     *
     * @return Collection|null
     */
    public static function findFromStringOfAnyType(string $name, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return static::query()
            ->where("name->{$locale}", $name)
            ->first();
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        if ('name' === $key && !is_array($value)) {
            return $this->setTranslation($key, app()->getLocale(), $value);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return int
     */
    public function getItemCountAttribute(): int
    {
        return $this->videos()->count();
    }

    /**
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        return optional($this->videos()->orderByDesc('created_at')->first(), function ($media) {
            return $media->thumbnail_url;
        });
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $type
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        if (is_null($type)) {
            return $query;
        }

        return $query->where('type', $type);
    }
}
