<?php

namespace App\Models;

use App\Support\Scout\Rules\SimpleMatchRule;
use App\Support\Scout\TagIndexConfigurator;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Resourceable;
use Illuminate\Support\Facades\DB;
use ScoutElastic\Searchable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel
{
    use Hashidable;
    use Randomable;
    use Resourceable;
    use Searchable;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
    ];

    /**
     * @var string
     */
    protected $indexConfigurator = TagIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        SimpleMatchRule::class,
    ];

    /**
     * @var array
     */
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only(['id', 'name']);
    }

    /**
     * @return morphedByMany
     */
    public function media()
    {
        return $this->morphedByMany(Media::class, 'taggable', 'taggables');
    }

    /**
     * @return string
     */
    public function getPlaceholderUrlAttribute(): string
    {
        return asset('storage/images/placeholders/empty.png');
    }

    /**
     * @return int
     */
    public function getTagCountByType($type = null): int
    {
        return DB::table('taggables')
            ->where('tag_id', $this->id)
            ->when($type, fn ($query, $type) => $query->where('taggable_type', $type))
            ->count();
    }

    /**
     * @param Builder $query
     * @param array   $tags
     * @param string  $type
     * @param string  $locale
     *
     * @return Builder
     */
    public function scopeWithSlugTranslated($query, array $tags = [], string $type = null, string $locale = null)
    {
        $useLocale = $locale ?? app()->getLocale();

        return $query
            ->when($type, fn ($query, $type) => $query->where('type', $type))
                ->where(function ($query) use ($tags, $useLocale) {
                    foreach ($tags as $tag) {
                        $query->orWhereJsonContains("slug->{$useLocale}", $tag);
                    }
                });
    }
}
