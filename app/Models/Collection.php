<?php

namespace App\Models;

use App\Traits\InteractsWithAcquaintances;
use Multicaret\Acquaintances\Traits\CanBeSubscribed;
use Spatie\Sluggable\HasTranslatableSlug;
use Spatie\Sluggable\SlugOptions;

class Collection extends BaseModel
{
    use CanBeSubscribed;
    use HasTranslatableSlug;
    use InteractsWithAcquaintances;

    /**
     * @var array
     */
    protected $with = ['media'];

    /**
     * @var array
     */
    public $translatable = ['name', 'slug', 'overview'];

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
        return $this->only([
            'id',
            'name',
            'type',
            'overview',
        ]);
    }

    /**
     * @return mixed
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
     * @return mixed
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
