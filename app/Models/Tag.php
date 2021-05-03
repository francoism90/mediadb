<?php

namespace App\Models;

use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Tags\Tag as BaseTag;

class Tag extends BaseTag implements Viewable
{
    use HasPrefixedId;
    use HasRandomSeed;
    use HasViews;
    use InteractsWithViews;
    use Searchable;

    /**
     * @var array
     */
    public array $translatable = ['name', 'slug', 'description'];

    /**
     * Delete all views of an viewable Eloquent model on delete.
     *
     * @var bool
     */
    protected bool $removeViewsOnDelete = true;

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => array_values($this->getTranslations('name')),
            'description' => array_values($this->getTranslations('description')),
            'type' => $this->type,
        ];
    }

    /**
     * @return MorphToMany
     */
    public function videos(): MorphToMany
    {
        return $this->morphedByMany(
            Video::class,
            'taggable',
            'taggables'
        );
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
}
