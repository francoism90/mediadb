<?php

namespace App\Models;

use App\Support\Scout\Rules\SimpleMatchRule;
use App\Support\Scout\TagIndexConfigurator;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use Illuminate\Support\Facades\DB;
use ScoutElastic\Searchable;
use Spatie\Tags\Tag as TagModel;

class Tag extends TagModel
{
    use Hashidable;
    use Randomable;
    use Searchable;

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
     * Retrieve the model for a bound value.
     *
     * @param mixed       $value
     * @param string|null $field
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        return $this->findByHash($value);
    }

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
    public function videos()
    {
        return $this->morphedByMany(
            'App\Models\Video',
            'taggable',
            'taggables'
        );
    }

    /**
     * @return morphedByMany
     */
    public function collections()
    {
        return $this->morphedByMany(
            'App\Models\Collection',
            'taggable',
            'taggables'
        );
    }

    /**
     * @return int
     */
    public function getItemCountAttribute($type = null): int
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
