<?php

namespace App\Traits;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Spatie\Tags\HasTags;

trait InteractsWithTags
{
    use HasTags {
        HasTags::getTagClassName as private getTagClassNameParent;
        HasTags::tags as private getTagsParent;
    }

    /**
     * @return string
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
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
    public function extractTagTranslations(string $field = 'slug'): array
    {
        $tagTranslations = $this->tagTranslations()->get();

        $tagSlugs = $tagTranslations->flatMap(function ($items) use ($field) {
            $tags = json_decode($items["{$field}_translated"], true);

            return array_values($tags);
        });

        return $tagSlugs->unique()->toArray();
    }
}
