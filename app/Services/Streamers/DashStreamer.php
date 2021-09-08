<?php

namespace App\Services\Streamers;

use App\Contracts\StreamerInterface;
use App\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DashStreamer implements StreamerInterface
{
    public function __construct(
        protected Model $model
    ) {
    }

    public function getManifestUrl(): string
    {
        return $this->getUrl('dash', 'manifest.mpd');
    }

    public function getThumbnailUrl(float $timeSecs): string
    {
        $uri = sprintf(
            'thumb-%d-w%d-h%d.jpg',
            round($timeSecs * 1000),
            config('api.conversions.sprite.width', 160),
            config('api.conversions.sprite.height', 90),
        );

        return $this->getUrl('thumb', $uri);
    }

    public function getManifestContents(): Collection
    {
        $sequences = collect([
            $this->getVideoSequence()->toArray(),
            $this->getCaptionSequence()->toArray(),
        ]);

        return collect([
            'id' => $this->model->getRouteKey(),
            'sequences' => $sequences->filter()->values(),
        ]);
    }

    public function getSpriteContents(): string
    {
        $duration = $this->model->duration ?? $this->model->clip?->duration ?? 0;

        $steps = config('api.conversions.sprite.steps', 5);

        $range = collect(range(1, $duration, $steps));

        $contents = "WEBVTT\n\n";

        foreach ($range as $timeCode) {
            $next = $range->after($timeCode, $duration);

            $contents .= sprintf(
                "%s --> %s\n%s\n\n",
                gmdate('H:i:s.v', $timeCode),
                gmdate('H:i:s.v', $next),
                $this->getThumbnailUrl($timeCode),
            );
        }

        return $contents;
    }

    protected function getVideoSequence(): Collection
    {
        return $this->model->getMedia('clip')->flatMap(function (Media $media) {
            return [
                'label' => $media->getRouteKey(),
                'clips' => [
                    [
                        'type' => 'source',
                        'path' => $media->getPath(),
                    ],
                ],
            ];
        });
    }

    protected function getCaptionSequence(): Collection
    {
        return $this->model->getMedia('caption')->flatMap(function (Media $media, $index) {
            return [
                'id' => sprintf('CC%d', $index + 1),
                'label' => $media->getRouteKey(),
                'language' => $media->getCustomProperty('locale', 'eng'),
                'clips' => [
                    [
                        'type' => 'source',
                        'path' => $media->getPath(),
                    ],
                ],
            ];
        });
    }

    protected function getUrl(string $location, string $uri): string
    {
        $hash = $this->getManifestHash($uri);

        $hashPath = $this->getEncryptedPath($hash);
        $hashPath = $this->getEncodedPath($hashPath);

        return sprintf('%s/%s/%s', config('api.vod.url'), $location, $hashPath);
    }

    protected function getManifestHash(string $uri): string
    {
        $path = sprintf('%s/%s', $this->getManifestRoute(), $uri);

        $hash = substr(
            md5($path, true),
            0,
            $this->getStreamHashSize()
        );

        // Add hash prefix
        $path = $hash.$path;

        // Add PKCS#7 padding
        $pad = 16 - (strlen($path) % 16);

        return $path.str_repeat(chr($pad), $pad);
    }

    protected function getManifestRoute(): string
    {
        $route = route('api.vod.manifest', ['video' => $this->model], false);

        return trim($route, '/');
    }

    protected function getEncryptedPath(string $path): string
    {
        return openssl_encrypt(
            $path,
            'aes-256-cbc',
            $this->getStreamKey(),
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            $this->getStreamIV()
        );
    }

    protected function getEncodedPath(string $path): string
    {
        return rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
    }

    protected function getStreamKey(): string
    {
        return pack('H*', config('api.vod.key'));
    }

    protected function getStreamIV(): string
    {
        return pack('H*', config('api.vod.iv'));
    }

    protected function getStreamHashSize(): int
    {
        return config('api.vod.hash_size', 8);
    }
}
