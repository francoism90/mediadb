<?php

namespace App\Models;

use App\Support\Scout\Rules\MultiMatchRule;
use App\Support\Scout\UserIndexConfigurator;
use App\Traits\Activityable;
use App\Traits\Randomable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\Models\Media;

class User extends Authenticatable implements HasMedia, Viewable
{
    use Activityable;
    use Notifiable;
    use Randomable;
    use Searchable;
    use Sluggable;
    use SluggableScopeHelpers;
    use SoftDeletes;
    use InteractsWithMedia;
    use InteractsWithViews;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @var string
     */
    protected $indexConfigurator = UserIndexConfigurator::class;

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
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only(['id', 'name', 'description']);
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('videos')->useDisk('media');
        $this->addMediaCollection('tracks')->useDisk('media');
    }

    /**
     * @param Media $media
     */
    public function registerMediaConversions($media = null): void
    {
        $this->addMediaConversion('thumbnail')
            ->width(480)
            ->height(320)
            ->extractVideoFrameAtSecond($media->getCustomProperty('snapshot', 10))
            ->performOnCollections('videos');
    }

    /**
     * @return hasMany
     */
    public function collections()
    {
        return $this->hasMany('App\Models\Collection');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        return asset('storage/images/placeholders/empty.png');
    }

    /**
     * @param array $items
     *
     * @return Collection
     */
    public function createCollections(array $items = [])
    {
        $collections = collect();

        foreach ($items as $item) {
            $model = $this->collections()->firstOrCreate(
                ['name' => $item['name']]
            );

            $collections->push($model);
        }

        return $collections;
    }
}
