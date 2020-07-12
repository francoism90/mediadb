<?php

namespace App\Models;

use App\Support\Scout\Rules\MultiMatchRule;
use App\Support\Scout\UserIndexConfigurator;
use App\Traits\Activityable;
use App\Traits\Channelable;
use App\Traits\Hashidable;
use App\Traits\Playlistable;
use App\Traits\Randomable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Multicaret\Acquaintances\Traits\CanFollow;
use ScoutElastic\Searchable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements Viewable
{
    use Activityable;
    use CanFollow;
    use Channelable;
    use Hashidable;
    use HasRoles;
    use InteractsWithViews;
    use Notifiable;
    use Playlistable;
    use Randomable;
    use Searchable;
    use Sluggable;
    use SluggableScopeHelpers;
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
    public function sluggable(): array
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
     * @return morphMany
     */
    public function channels()
    {
        return $this->morphMany('App\Models\Channel', 'model');
    }

    /**
     * @return belongsToJson
     */
    public function playlists()
    {
        return $this->morphMany('App\Models\Playlist', 'model');
    }

    /**
     * @return string
     */
    public function getThumbnailAttribute(): string
    {
        return '';
    }
}
