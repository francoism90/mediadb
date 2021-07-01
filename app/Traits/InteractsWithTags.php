<?php

namespace App\Traits;

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

    public function extractTagTranslations(): array
    {
        $tagTranslations = $this->tagTranslations()->get();

        $collection = $tagTranslations->flatMap(function ($tags) {
            $names = json_decode($tags['name_translated'], true);
            $descriptions = json_decode($tags['description_translated'], true);

            return array_merge($names, $descriptions);
        });

        return $collection->unique()->toArray();
    }
}
