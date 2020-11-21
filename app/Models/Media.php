<?php

namespace App\Models;

use App\Traits\HasHashids;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\ModelStatus\HasStatuses;

class Media extends BaseMedia
{
    use HasHashids;
    use HasStatuses;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

    /**
     * @return string
     */
    public function getKindAttribute(): string
    {
        return Str::plural($this->collection_name);
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        return $this->getUrl();
    }

    /**
     * @return string
     */
    public function getLocaleAttribute(): string
    {
        return $this->getCustomProperty('locale', 'undefined');
    }
}
