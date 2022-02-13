<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
use App\Traits\InteractsWithHashids;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Multicaret\Acquaintances\Traits\CanFavorite;
use Multicaret\Acquaintances\Traits\CanFollow;
use Multicaret\Acquaintances\Traits\CanSubscribe;
use Multicaret\Acquaintances\Traits\CanView;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
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
    use HasRoles;
    use HasSchemalessAttributes;
    use HasSlug;
    use InteractsWithHashids;
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

    public function receivesBroadcastNotificationsOn(): string
    {
        return sprintf('user.%s', $this->getRouteKey());
    }

    public function videos(): MorphMany
    {
        return $this->morphMany(Video::class, 'model');
    }

    public function searchableAs(): string
    {
        return 'users_index';
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'description' => $this->description,
            'created' => $this->created_at,
            'updated' => $this->updated_at,
        ];
    }

    public function preferredLocale(): string
    {
        return $this->extra_attributes->get('locale', config('app.fallback_locale'));
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile()
            ->useDisk('media');
    }

    public function assignedRoles(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getRoleNames(),
        );
    }

    public function assignedPermissions(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getAllPermissions()?->pluck('name'),
        );
    }

    public function avatarUrl(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getFirstMediaUrl('avatar'),
        );
    }

    protected function makeAllSearchableUsing(Builder $query): Builder
    {
        return $query->with($this->with);
    }
}
