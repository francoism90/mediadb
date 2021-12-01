<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use App\Traits\InteractsWithTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Tags\Tag as BaseTag;

class Tag extends BaseTag
{
    use HasPrefixedId;
    use HasSchemalessAttributes;
    use InteractsWithTranslations;
    use Searchable;

    public array $translatable = [
        'name',
        'slug',
        'description',
    ];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function videos(): MorphToMany
    {
        return $this->morphedByMany(
            Video::class, 'taggable', 'taggables'
        );
    }

    public function searchableAs(): string
    {
        return 'tags_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->prefixed_id,
            'name' => $this->extractTranslations('name'),
            'description' => $this->extractTranslations('description'),
            'items' => $this->items,
            'type' => $this->type,
        ];
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }

    public function getItemsAttribute(?string $type = null): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->when($type, fn ($query, $type) => $query->where('taggable_type', $type))
            ->count();
    }
}
