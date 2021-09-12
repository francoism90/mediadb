<?php

namespace App\Traits;

use App\Models\Media;
use App\Services\VideoDashService;
use Illuminate\Support\Collection;

trait InteractsWithDash
{
    public function getDashUrlAttribute(): string
    {
        return app(VideoDashService::class)->generateUrl(
            $this,
            'dash',
            'manifest.mpd'
        );
    }

    public function frameCaptureUrl(float $time): string
    {
        $uri = sprintf(
            'thumb-%d-w%d-h%d.jpg',
            round($time * 1000),
            config('api.video.conversions.sprite.width', 160),
            config('api.video.conversions.sprite.height', 90),
        );

        return app(VideoDashService::class)->generateUrl(
            $this,
            'thumb',
            $uri
        );
    }

    public function getManifestContents(): Collection
    {
        $sequences = collect([
            $this->getClipsSequence()->toArray(),
            $this->getCaptionsSequence()->toArray(),
        ]);

        return collect([
            'id' => $this->getRouteKey(),
            'sequences' => $sequences->filter()->values(),
        ]);
    }

    public function getClipsSequence(): Collection
    {
        return $this->getMedia('clips')?->flatMap(function (Media $media) {
            return [
                'id' => $media->getRouteKey(),
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

    public function getCaptionsSequence(): Collection
    {
        return $this->getMedia('captions')?->flatMap(function (Media $media, $index) {
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
}
