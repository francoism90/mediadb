<?php

namespace App\Models;

use App\Support\Scout\Rules\MultiMatchRule;
use App\Support\Scout\UserIndexConfigurator;
use App\Traits\HasActivities;
use App\Traits\HasHashids;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Multicaret\Acquaintances\Traits\CanFollow;
use ScoutElastic\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements HasLocalePreference, HasMedia, Viewable
{
    use HasActivities;
    use HasApiTokens;
    use HasFactory;
    use HasHashids;
    use HasRandomSeed;
    use HasRoles;
    use HasViews;
    use InteractsWithMedia;
    use InteractsWithViews;
    use CanFollow;
    use Notifiable;
    use Searchable;

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
     * Get the user's preferred locale.
     *
     * @return string
     */
    public function preferredLocale()
    {
        return data_get($this, 'custom_properties.locale', config('app.fallback_locale'));
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
     * @return void
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')
             ->singleFile()
             ->useDisk('media');
    }

    /**
     * @return array
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    /**
     * @return mixed
     */
    public function videos()
    {
        return $this->morphMany('App\Models\Video', 'model');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        // TODO: add thumbnail support
        return '';
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
}
