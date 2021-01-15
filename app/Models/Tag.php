<?php

namespace App\Models;

use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithElasticsearch;
use App\Traits\InteractsWithHashids;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel implements Viewable
{
    use HasRandomSeed;
    use HasViews;
    use InteractsWithElasticsearch;
    use InteractsWithHashids;
    use InteractsWithViews;
    use QueryCacheable;
    use Searchable;

    /**
     * @var int
     */
    public int $cacheFor = 3600;

    /**
     * @var array
     */
    public $translatable = ['name', 'slug', 'description'];

    /**
     * Delete all views of an viewable Eloquent model on delete.
     *
     * @var bool
     */
    protected bool $removeViewsOnDelete = true;

    /**
     * Invalidate the cache automatically upon update.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only([
            'id',
            'name',
            'description',
        ]);
    }

    /**
     * @return MorphToMany
     */
    public function videos(): MorphToMany
    {
        return $this
            ->morphedByMany(Video::class, 'taggable', 'taggables');
    }

    /**
     * @param string $type
     *
     * @return int
     */
    public function getItemsAttribute(string $type = null): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->when($type, fn ($query, $type) => $query->where('taggable_type', $type))
            ->count();
    }

    /**
     * @param Builder $query
     * @param array   ...$values
     *
     * @return Builder
     */
    public function scopeWithSlug(
        Builder $query,
        ...$values
    ): Builder {
        $locale = app()->getLocale();

        return $query
            ->whereIn("slug->{$locale}", $values);
    }
}
