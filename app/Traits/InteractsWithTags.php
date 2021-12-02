<?php

namespace App\Traits;

use App\Models\Tag;
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

    public function extractTagTranslations(string $field = 'name', ?string $type = null): array
    {
        $collect = $this
            ->tags
            ->when($type, fn ($query, $type) => $query->where('type', $type))
            ->map(fn (Tag $item) => $item->$field)
            ->unique();

        return $collect->values()->all();
    }
}
