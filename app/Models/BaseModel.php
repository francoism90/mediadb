<?php

namespace App\Models;

use App\Traits\HasCustomProperties;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithActivities;
use App\Traits\InteractsWithHashids;
use App\Traits\InteractsWithTags;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStatus\HasStatuses;
use Spatie\Translatable\HasTranslations;

abstract class BaseModel extends Model implements HasMedia, Viewable
{
    use HasCustomProperties;
    use HasFactory;
    use HasRandomSeed;
    use HasStatuses;
    use HasTranslations;
    use HasViews;
    use InteractsWithActivities;
    use InteractsWithHashids;
    use InteractsWithMedia;
    use InteractsWithTags;
    use InteractsWithViews;
    use Notifiable;
    use QueryCacheable;

    /**
     * @var int
     */
    public int $cacheFor = 3600;

    /**
     * Delete all views of an viewable Eloquent model on delete.
     *
     * @var bool
     */
    protected bool $removeViewsOnDelete = true;

    /**
     * @var array
     */
    protected $casts = [
        'custom_properties' => 'json',
    ];

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * Invalidate the cache automatically upon update.
     *
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;
}
