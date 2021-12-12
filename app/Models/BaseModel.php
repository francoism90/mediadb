<?php

namespace App\Models;

use App\Traits\HasSchemalessAttributes;
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
    use HasFactory;
    use HasPrefixedId;
    use HasSchemalessAttributes;
    use HasTranslations;
    use InteractsWithMedia;
    use InteractsWithTags;
    use InteractsWithTranslations;
    use Notifiable;
    use QueryCacheable;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected static $flushCacheOnUpdate = true;

    public function getRouteKeyName(): string
    {
        return 'prefixed_id';
    }
}
