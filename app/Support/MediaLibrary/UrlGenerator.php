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
                $this->conversion ? $this->conversion->getName() : null,
            ]
        );
    }

    /**
     * @param \DateTimeInterface $expiration
     * @param array              $options
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
                $this->conversion ? $this->conversion->getName() : null,
            ]
        );
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        $adapter = $this->getDisk()->getAdapter();

        $cachedAdapter = '\League\Flysystem\Cached\CachedAdapter';

        if ($adapter instanceof $cachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = $adapter->getPathPrefix();

        return $pathPrefix;
    }

    /**
     * @return string
     */
    public function getBaseMediaPath(): string
    {
        $pathPrefix = $this->getBasePath();

        return $pathPrefix.$this->pathGenerator->getPath($this->media);
    }

    /**
     * @return string
     */
    public function getBaseMediaDirectoryUrl()
    {
        return $this->getDisk()->url('/');
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        $pathPrefix = $this->getBasePath();

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
