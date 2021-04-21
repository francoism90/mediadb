<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;

trait InteractsWithTags
{
    use HasTags {
        HasTags::tags as private getBaseTags;
    }

    /**
     * @return mixed
     */
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

    /**
     * @return MorphToMany
     */
    public function tagTranslations(): MorphToMany
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable')
            ->select('*')
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(name, '$.*')) as name_translated")
            ->selectRaw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.*')) as slug_translated")
            ->ordered();
    }

    /**
     * @param string $field
     *
     * @return array
     */
    public function extractTagTranslations(string $field = 'name'): array
    {
        $tagTranslations = $this->tagTranslations()->get();

        $collection = $tagTranslations->flatMap(function ($tags) use ($field) {
            $tagTranslated = json_decode($tags["{$field}_translated"], true);

            return array_values($tagTranslated);
        });

        return $collection->unique()->toArray();
    }
}
