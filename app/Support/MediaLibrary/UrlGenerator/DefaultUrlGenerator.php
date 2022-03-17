<?php

namespace App\Support\MediaLibrary\UrlGenerator;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

    public function getBaseMediaDirectoryUrl(): string
    {
        return $this->getDisk()->url('/');
    }

    public function getPath(): string
    {
        return $this->getRootOfDisk().$this->getPathRelativeToRoot();
    }

    public function getResponsiveImagesDirectoryUrl(): string
    {
        $base = Str::finish($this->getBaseMediaDirectoryUrl(), '/');

        $path = $this->pathGenerator->getPathForResponsiveImages($this->media);

        return Str::finish(url($base.$path), '/');
    }

    protected function getRootOfDisk(): string
    {
        return $this->getDisk()->path('/');
    }
}
