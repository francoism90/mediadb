<?php

namespace App\Models;

use App\Traits\HasRandomSeed;
use App\Traits\HasSchemalessAttributes;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanFavorite;
use Multicaret\Acquaintances\Traits\CanFollow;
use Multicaret\Acquaintances\Traits\CanSubscribe;
use Multicaret\Acquaintances\Traits\CanView;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class User extends Authenticatable implements HasLocalePreference, HasMedia
{
    use CanFavorite;
    use CanFollow;
    use CanSubscribe;
    use CanView;
    use HasApiTokens;
    use HasFactory;
    use HasPrefixedId;
    use HasRandomSeed;
    use HasRoles;
    use HasSchemalessAttributes;
    use HasSlug;
    use InteractsWithMedia;
    use Notifiable;
    use Searchable;

    /**
     * @var array
     */
    protected $with = [
        'media',
        'permissions',
        'roles',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }

    public function searchableAs(): string
    {
        return 'users_index';
    }

    public function toSearchableArray(): array
    {
        return $this->only([
            'id',
            'name',
            'email',
            'description',
        ]);
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(Video::class, 'model');
    }

    public function preferredLocale(): string
    {
        return $this->extra_attributes->get(
            'locale', config('app.fallback_locale')
        );
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'user.'.$this->getRouteKey();
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk('media');
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->getFirstMediaUrl('avatar');
    }

    public function getAssignedRolesAttribute(): Collection
    {
        return $this->getRoleNames();
    }

    public function getAssignedPermissionsAttribute(): Collection
    {
        return $this->getAllPermissions()->pluck('name');
    }

    public function getSettingsAttribute(): ?array
    {
        return $this->extra_attributes->get(
            'settings', config('api.default_settings')
        );
    }
}
