<?php

namespace App\Models;

use App\Traits\HasQueryCacheable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
    use HasQueryCacheable;

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
        'metadata',
        'type',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getMetadataAttribute(): array
    {
        return Arr::only($this->custom_properties, [
            'bitrate',
            'codec_name',
            'codec_height',
            'codec_width',
            'display_aspect_ratio',
            'duration',
            'probe_score',
            'start_time',
            'height',
            'width',
        ]);
    }

    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
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
