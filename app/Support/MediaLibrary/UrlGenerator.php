<?php

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

class UrlGenerator extends BaseUrlGenerator
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return URL::signedRoute(
            'api.media.download',
            [
                $this->media,
                auth()->user(),
            ]
        );
    }

    /**
     * @param DateTimeInterface $expiration
     * @param array             $options
     *
     * @return string
     */
    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return URL::temporarySignedRoute(
            'api.media.download',
            $expiration,
            [
                $this->media,
                auth()->user(),
            ]
        );
    }

    /**
     * @return string
     */
    public function getBaseMediaDirectoryUrl(): string
    {
        return $this->getDisk()->url('/');
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        $adapter = $this->getDisk()->getAdapter();

        $cachedAdapter = '\League\Flysystem\Cached\CachedAdapter';

        if ($adapter instanceof $cachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = $adapter->getPathPrefix();

        return $pathPrefix.$this->getPathRelativeToRoot();
    }

    /**
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base.$path), '/');
    }
}
