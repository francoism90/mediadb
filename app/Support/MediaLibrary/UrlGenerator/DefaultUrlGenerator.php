<?php

namespace App\Support\MediaLibrary\UrlGenerator;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use League\Flysystem\Adapter\AbstractAdapter;
use Spatie\MediaLibrary\Support\UrlGenerator\BaseUrlGenerator;

class DefaultUrlGenerator extends BaseUrlGenerator
{
    public function getUrl(): string
    {
        return URL::signedRoute(
            'api.media.response',
            [
                'media' => $this->media,
                // 'conversion' => $this->conversion?->getName(),
                'version' => $this->media?->updated_at?->timestamp,
            ]
        );
    }

    public function getTemporaryUrl(DateTimeInterface $expiration, array $options = []): string
    {
        return URL::temporarySignedRoute(
            'api.media.response',
            $expiration,
            [
                'media' => $this->media,
                // 'conversion' => $this->conversion?->getName(),
                'version' => $this->media?->updated_at?->timestamp,
            ]
        );
    }

    public function getBaseMediaDirectoryUrl()
    {
        return $this->getDisk()->url('/');
    }

    public function getPath(): string
    {
        $adapter = $this->getDisk()->getAdapter();

        $cachedAdapter = '\League\Flysystem\Cached\CachedAdapter';

        if ($adapter instanceof $cachedAdapter) {
            $adapter = $adapter->getAdapter();
        }

        $pathPrefix = '';

        if ($adapter instanceof AbstractAdapter) {
            /** @var AbstractAdapter $pathPrefix */
            $pathPrefix = $adapter->getPathPrefix();
        }

        return $pathPrefix.$this->getPathRelativeToRoot();
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base.$path), '/');
    }
}
