<?php

namespace App\Support\MediaLibrary;

use DateTimeInterface;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Exceptions\UrlCannotBeDetermined;
use Spatie\MediaLibrary\UrlGenerator\BaseUrlGenerator;

class LocalUrlGenerator extends BaseUrlGenerator
{
    /**
     * @return string
     */
    public function getUrl(): string
    {
        return URL::signedRoute('api.asset.show', [
            $this->media,
            auth()->user(),
        ]);
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
            'api.asset.show',
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
    public function getPath(): string
    {
        return $this->getStoragePath().'/'.$this->getPathRelativeToRoot();
    }

    /**
     * @return string
     */
    protected function getBaseMediaDirectoryUrl(): string
    {
        if ($diskUrl = $this->config->get("filesystems.disks.{$this->media->disk}.url")) {
            return str_replace(url('/'), '', $diskUrl);
        }

        if (!Str::startsWith($this->getStoragePath(), public_path())) {
            throw UrlCannotBeDetermined::mediaNotPubliclyAvailable($this->getStoragePath(), public_path());
        }

        return $this->getBaseMediaDirectory();
    }

    /**
     * @return string
     */
    protected function getBaseMediaDirectory(): string
    {
        return str_replace(public_path(), '', $this->getStoragePath());
    }

    /**
     * @return string
     */
    protected function getStoragePath(): string
    {
        $diskRootPath = $this->config->get("filesystems.disks.{$this->media->disk}.root");

        return realpath($diskRootPath);
    }

    /**
     * @return string
     */
    public function getResponsiveImagesDirectoryUrl(): string
    {
        return url($this->getBaseMediaDirectoryUrl().'/'.$this->pathGenerator->getPathForResponsiveImages($this->media)).'/';
    }
}
