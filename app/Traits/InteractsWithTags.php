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

    public function extractTagTranslations(string $field = 'name'): array
    {
        $tagTranslations = $this->tagTranslations()->get();

        $collection = $tagTranslations->flatMap(function ($tags) use ($field) {
            $translations = (array) json_decode($tags[sprintf('%s_translated', $field)], true);

            return array_values($translations);
        });

        return $collection->unique()->toArray();
    }

    public function scopeWithTags(Builder $query, ...$values)
    {
        return $this->scopeWithAllTagsOfAnyType($query, $values);
    }
}
