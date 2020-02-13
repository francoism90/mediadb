<?php

namespace App\Models;

use App\Traits\Hashidable;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel implements ViewableContract
{
    use Hashidable;
    use Viewable;

    /**
     * @return morphedByMany
     */
    public function collections()
    {
        return $this->morphedByMany(Collection::class, 'taggable', 'taggables');
    }

    /**
     * @return morphedByMany
     */
    public function media()
    {
        return $this->morphedByMany(Media::class, 'taggable', 'taggables');
    }

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->unique()->count();
    }

    /**
     * @param Builder $query
     * @param array   $tags
     * @param string  $type
     * @param string  $locale
     *
     * @return Builder
     */
    public function scopeWithSlugTranslated($query, array $tags = [], string $type = null, string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->where(function ($query) use ($tags, $locale) {
                foreach ($tags as $tag) {
                    $query->orWhereJsonContains("slug->{$locale}", $tag);
                }
            });
    }
}
