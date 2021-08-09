<?php

namespace App\Models;

use App\Traits\HasCustomProperties;
use App\Traits\HasRandomSeed;
use App\Traits\InteractsWithTags;
use App\Traits\InteractsWithTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\PrefixedIds\Models\Concerns\HasPrefixedId;
use Spatie\Translatable\HasTranslations;

abstract class BaseModel extends Model implements HasMedia
{
    use HasCustomProperties;
    use HasFactory;
    use HasPrefixedId;
    use HasRandomSeed;
    use HasTranslations;
    use InteractsWithMedia;
    use InteractsWithTags;
    use InteractsWithTranslations;
    use Notifiable;
    use QueryCacheable;

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

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }
}
