<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;

trait InteractsWithTags
{
    use HasTags {
        HasTags::tags as private getBaseTags;
    }

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

    public function tagTranslations(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable')
            ->select('*')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.*')) as name_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.*')) as slug_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(description, '$.*')) as description_translated")
            ->ordered();
    }

    public function extractTagTranslations(string $field = 'name', ?string $type = null): array
    {
        $collect = $this
            ->tagTranslations()
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->pluck(sprintf('%s_translated', $field))
            ->flatMap(fn ($tags) => collect($tags)->toArray());

        return $collect->unique()->toArray();
    }

    public function scopeWithTags(Builder $query, ...$values)
    {
        return $this
            ->scopeWithAllTagsOfAnyType($query, $values)
            ->inRandomOrder();
    }
}
