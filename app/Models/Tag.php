<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use App\Traits\InteractsWithAcquaintances;
use App\Traits\InteractsWithTranslations;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanBeFavorited;
use Multicaret\Acquaintances\Traits\CanBeFollowed;
use Multicaret\Acquaintances\Traits\CanBeViewed;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Tags\Tag as BaseTag;

class Tag extends BaseTag
{
    use CanBeFavorited;
    use CanBeFollowed;
    use CanBeViewed;
    use HasPrefixedId;
    use HasSchemalessAttributes;
    use InteractsWithAcquaintances;
    use InteractsWithTranslations;
    use Searchable;
    use QueryCacheable;

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
            'order' => $this->order_column,
            'items' => $this->items,
            'type' => $this->type,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
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

    public function scopeActive(Builder $query): Builder
    {
        // TODO: check 'published' status
        return $query;
    }

    public function scopeWithAnyType(Builder $query, ?array $types = [], ?User $user = null): Builder
    {
        $type = fn (string $key) => in_array($key, $types);

        return Tag::active()
            ->when($type('actor'), fn ($query) => $query->withType('actor'))
            ->when($type('genre'), fn ($query) => $query->withType('genre'))
            ->when($type('language'), fn ($query) => $query->withType('language'))
            ->when($type('studio'), fn ($query) => $query->withType('studio'))
            ->when($type('favorites'), fn ($query) => $query->userFavorites($user))
            ->when($type('following'), fn ($query) => $query->userFollowing($user))
            ->when($type('ordered'), fn ($query) => $query->orderBy('order_column'))
            ->when($type('random'), fn ($query) => $query->inRandomOrder())
            ->when($type('viewed'), fn ($query) => $query->userViewed($user));
    }
}
