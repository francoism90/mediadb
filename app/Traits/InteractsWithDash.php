<?php

namespace App\Traits;

use App\Models\Media;
use App\Services\VideoDashService;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

trait InteractsWithDash
{
    public function getDashManifestUrl(): string
    {
        return app(VideoDashService::class)->generateUrl(
            $this,
            'dash',
            'manifest.mpd'
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
        return $this->getClips()?->flatMap(function (Media $media): array {
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
        return $this->getCaptions()?->flatMap(function (Media $media, $index): array {
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

    public function getClips(): MediaCollection
    {
        return $this->getMedia('clips')
            ?->sortByDesc([
                ['custom_properties->height', 'desc'],
                ['custom_properties->width', 'desc'],
            ]);
    }

    public function getClip(): ?Media
    {
        return $this->getClips()?->first();
    }

    public function getCaptions(): MediaCollection
    {
        return $this->getMedia('captions');
    }

    public function getVideoResolution(?Media $media): ?string
    {
        $collect = collect(config('api.video.resolutions'));

        $byHeight = $collect->firstWhere('height', '>=', $media?->getCustomProperty('height') ?? 0);
        $byWidth = $collect->firstWhere('width', '>=', $media?->getCustomProperty('width') ?? 0);

        return $byHeight['name'] ?? $byWidth['name'];
    }

    public function frameCaptureUrl(float $time): string
    {
        $uri = sprintf(
            'thumb-%d-w%d-h%d.jpg',
            round($time * 1000),
            config('api.video.conversions.sprite.width', 240),
            config('api.video.conversions.sprite.height', 120),
        );

        return app(VideoDashService::class)->generateUrl($this, 'thumb', $uri);
    }
}
