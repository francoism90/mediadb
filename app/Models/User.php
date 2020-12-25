<?php

namespace App\Models;

use App\Traits\HasActivities;
use App\Traits\HasCustomProperties;
use App\Traits\HasHashids;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanFavorite;
use Multicaret\Acquaintances\Traits\CanLike;
use Multicaret\Acquaintances\Traits\CanSubscribe;
use Rennokki\QueryCache\Traits\QueryCacheModule;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements HasLocalePreference, HasMedia, Viewable
{
    use CanFavorite;
    use CanLike;
    use CanSubscribe;
    use HasActivities;
    use HasApiTokens;
    use HasCustomProperties;
    use HasFactory;
    use HasHashids;
    use HasRandomSeed;
    use HasRoles;
    use HasSlug;
    use HasViews;
    use InteractsWithMedia;
    use InteractsWithViews;
    use Notifiable;
    use Searchable;
    use QueryCacheModule;

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
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    /**
     * @var int
     */
    public $cacheFor = 3600;

    /**
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return data_get($this, 'custom_properties.locale', config('app.fallback_locale'));
    }

    /**
     * @return SlugOptions
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    public function receivesBroadcastNotificationsOn()
    {
        return 'user.'.$this->getRouteKey();
    }

    /**
     * @return MorphMany
     */
    public function videos(): MorphMany
    {
        return $this->morphMany(Video::class, 'model');
    }

    /**
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
             ->singleFile()
             ->useDisk('media');
    }

    /**
     * @return string
     */
    public function searchableAs()
    {
        return 'users';
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return $this->only([
            'id',
            'name',
            'email',
            'description',
        ]);
    }

    /**
     * @return string|null
     */
    public function getAvatarAttribute()
    {
        return $this->getFirstMediaUrl('avatar');
    }

    /**
     * @return array
     */
    public function getAssignedRolesAttribute(): array
    {
        return [
            'roles' => $this->getRoleNames()->toArray(),
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
        ];
    }

    /**
     * @return mixed
     */
    public function getSettingsAttribute()
    {
        return $this->getCustomProperty('settings');
    }
}
