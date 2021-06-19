<?php

namespace App\Models;

use App\Traits\HasCustomProperties;
use App\Traits\HasRandomSeed;
use App\Traits\HasViews;
use App\Traits\InteractsWithTags;
use App\Traits\InteractsWithTranslations;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\ModelStatus\HasStatuses;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Translatable\HasTranslations;

abstract class BaseModel extends Model implements HasMedia, Viewable
{
    use HasCustomProperties;
    use HasFactory;
    use HasPrefixedId;
    use HasRandomSeed;
    use HasStatuses;
    use HasTranslations;
    use HasViews;
    use InteractsWithMedia;
    use InteractsWithTags;
    use InteractsWithTranslations;
    use InteractsWithViews;
    use Notifiable;

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

    protected bool $removeViewsOnDelete = true;

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }
}
