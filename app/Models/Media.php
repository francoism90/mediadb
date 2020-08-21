<?php

namespace App\Models;

use App\Traits\Hashidable;
use App\Traits\Randomable;
use App\Traits\Viewable as ViewableHelpers;
use Cviebrock\EloquentSluggable\Sluggable;
use Cviebrock\EloquentSluggable\SluggableScopeHelpers;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;
use Spatie\MediaLibrary\Support\UrlGenerator\UrlGeneratorFactory;
use Spatie\ModelStatus\HasStatuses;

class Media extends BaseMedia implements Viewable
{
    use Hashidable;
    use HasStatuses;
    use InteractsWithViews;
    use Randomable;
    use Sluggable;
    use SluggableScopeHelpers;
    use ViewableHelpers;

    /**
     * @var array
     */
    protected $guarded = [];

    /**
     * @var bool
     */
    protected $removeViewsOnDelete = true;

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
     * @return string
     */
    public function getBasePath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBasePath();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath(): string
    {
        $urlGenerator = UrlGeneratorFactory::createForMedia($this);

        return $urlGenerator->getBaseMediaPath();
    }

    /**
     * @return string
     */
    public function getDownloadUrlAttribute(): string
    {
        $expires = now()->addSeconds(
            config('vod.expire')
        );

        return $this->getTemporaryUrl($expires);
    }
}
