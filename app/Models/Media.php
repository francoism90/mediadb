<?php

namespace App\Models;

use App\Support\Scout\MediaIndexConfigurator;
use App\Support\Scout\Rules\MultiMatchRule;
use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Resourceable;
use App\Traits\Securable;
use App\Traits\Streamable;
use App\Traits\Taggable;
use App\Traits\Viewable as ViewableHelpers;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable as ViewableContract;
use CyrildeWit\EloquentViewable\Viewable;
use Illuminate\Support\Facades\URL;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\Models\Media as BaseMedia;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Tags\HasTags;
use Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class Media extends BaseMedia implements ViewableContract
{
    use Hashidable;
    use HasJsonRelationships;
    use HasStatuses;
    use HasTags;
    use Randomable;
    use Resourceable;
    use Searchable;
    use Sluggable;
    use Securable;
    use SluggableScopeHelpers;
    use Streamable;
    use Taggable;
    use Viewable;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var string
     */
    protected $indexConfigurator = MediaIndexConfigurator::class;

    /**
     * @var array
     */
    protected $searchRules = [
        MultiMatchRule::class,
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
            'description' => [
                'type' => 'text',
                'analyzer' => 'autocomplete',
                'search_analyzer' => 'autocomplete_search',
            ],
        ],
    ];

    /**
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'name',
            ],
        ];
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only(['id', 'name', 'description']);
    }

    /**
     * @return string
     */
    public static function getTagClassName(): string
    {
        return Tag::class;
    }

    /**
     * @return MorphToMany
     */
    public function tags()
    {
        return $this
            ->morphToMany(self::getTagClassName(), 'taggable', 'taggables', null, 'tag_id')
            ->orderBy('order_column');
    }

    /**
     * @return hasManyJson
     */
    public function collections()
    {
        return $this->hasManyJson('App\Models\Collection', 'custom_properties->media_ids');
    }

    /**
     * @return int
     */
    public function getViewsAttribute(): int
    {
        return views($this)->remember()->unique()->count();
    }

    /**
     * @return string
     */
    public function getPlaceholderUrlAttribute(): string
    {
        if (!$this->hasGeneratedConversion('thumbnail')) {
            return asset('storage/images/placeholders/empty.png');
        }

        return URL::signedRoute('api.asset.placeholder', [
            'media' => $this,
            'user' => auth()->user(),
            'version' => $this->updated_at->timestamp,
        ]);
    }

    /**
     * @return string
     */
    public function getPreviewUrlAttribute(): string
    {
        if (!$this->hasGeneratedConversion('preview')) {
            return asset('storage/images/placeholders/blank.mp4');
        }

        return URL::signedRoute('api.asset.preview', [
            'media' => $this,
            'user' => auth()->user(),
            'version' => $this->updated_at->timestamp,
        ]);
    }

    /**
     * @return Collection
     */
    public function getUserCollectionsAttribute()
    {
        return $this->collections->where('user_id', auth()->user()->id ?? 0);
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getTemporaryUrl(
            Carbon::now()->addHours(4)
        );
    }

    /**
     * @return string
     */
    public function getStreamUrlAttribute(): string
    {
        return self::getSecureExpireLink(
            $this->getStreamUrl(),
            config('vod.secret'),
            config('vod.expire'),
            $this->getRouteKey(),
            request()->ip()
        );
    }

    /**
     * @return string
     */
    public function getStreamThumbUrlAttribute(int $offset = 1000, string $resize = 'w160-h100'): string
    {
        return self::getSecureExpireLink(
            $this->getStreamUrl('thumb', "thumb-{$offset}-{$resize}.jpg"),
            config('vod.secret'),
            config('vod.expire'),
            $this->getRouteKey()."_thumb_{$offset}",
            request()->ip()
        );
    }

    /**
     * @param array $items
     * @param User  $user
     *
     * @return void
     */
    public function syncCollections(array $items = [], User $user)
    {
        $attachTo = collect();

        // Validate each requested collection
        foreach ($items as $item) {
            $collect = $user->collections()->firstOrCreate(
                ['name' => $item['name'] ?? $item]
            );

            $attachTo->push($collect);
        }

        // Sync to user collections
        foreach ($user->collections as $collection) {
            if ($attachTo->firstWhere('id', $collection->id)) {
                $collection->media()->attach($this->id)->save();
            } else {
                $collection->media()->detach($this->id)->save();
            }
        }
    }
}
