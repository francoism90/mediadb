<?php

namespace App\Models;

use App\Traits\InteractsWithHashids;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function fileType(): Attribute
    {
        return new Attribute(
            get: fn () => strtok($this->mime_type, '/'),
        );
    }

    public function kind(): Attribute
    {
        return new Attribute(
            get: fn () => Str::plural($this->collection_name),
        );
    }

    public function properties(): Attribute
    {
        return new Attribute(
            get: fn () => Arr::only($this->custom_properties, config('api.media.visible_properties')),
        );
    }

    public function thumbnail(): Attribute
    {
        return new Attribute(
            get: fn () => $this->getCustomProperty('thumbnail'),
        );
    }
}
