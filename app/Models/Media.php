<?php

namespace App\Models;

use App\Traits\InteractsWithHashids;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use InteractsWithHashids;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var array
     */
    protected $touches = [
        'model',
    ];

    /**
     * @var array
     */
    protected $appends = [
        'properties',
        'type',
    ];

    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    public function getPropertiesAttribute(): array
    {
        return Arr::only(
            $this->custom_properties,
            config('api.media.visible_properties')
        );
    }

    public function getTypeAttribute(): string
    {
        return strtok($this->mime_type, '/');
    }

    public function getThumbnailAttribute(): ?string
    {
        return $this->getCustomProperty('thumbnail');
    }
}
