<?php

namespace App\Models;

use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithTranslations;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Builder;
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
    use InteractsWithTranslations;
    use InteractsWithViews;
    use Searchable;

    public array $translatable = [
        'name',
        'slug',
        'description',
    ];

    protected bool $removeViewsOnDelete = true;

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->extractTranslations('name'),
            'description' => $this->extractTranslations('description'),
            'type' => $this->type,
        ];
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(
            Video::class, 'taggable', 'taggables'
        );
    }

    public function getItemsAttribute(?string $type = null): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->when($type, fn ($query, $type) => $query->where('taggable_type', $type))
            ->count();
    }

    public function scopeWithSlug(Builder $query, ...$values): Builder
    {
        $locale = app()->getLocale();

        return $query->whereIn(sprintf('slug->%s', $locale), $values);
    }
}
