<?php

namespace App\Models;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class Media extends BaseMedia
{
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

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    public function getMetadataAttribute(): array
    {
        return Arr::only($this->custom_properties, [
            'bitrate',
            'codec_name',
            'duration',
            'probe_score',
            'start_time',
            'height',
            'width',
        ]);
    }

    public function getTypeAttribute(): string
    {
        return strtok($this->mime_type, '/');
    }
}
