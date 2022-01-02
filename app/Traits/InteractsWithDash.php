<?php

namespace App\Traits;

use App\Helpers\Arr;
use App\Models\Media;
use App\Services\VideoDashService;
use Illuminate\Support\Collection;

trait InteractsWithDash
{
    public function getDashUrlAttribute(): string
    {
        return app(VideoDashService::class)
            ->generateUrl($this, 'dash', 'manifest.mpd');
    }

    public function frameCaptureUrl(float $time): string
    {
        $uri = sprintf(
            'thumb-%d-w%d-h%d.jpg',
            round($time * 1000),
            config('api.video.conversions.sprite.width', 240),
            config('api.video.conversions.sprite.height', 120),
        );

        return app(VideoDashService::class)
            ->generateUrl($this, 'thumb', $uri);
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
        return $this->getMedia('clips')?->flatMap(function (Media $media): array {
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
        return $this->getMedia('captions')?->flatMap(function (Media $media, $index): array {
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

    public function getPreviewContents(): Collection
    {
        // Create durations
        $parts = $this->getPreviewsParts();

        $length = $this->getPreviewsLength();

        $durations = array_fill(0, $parts, $length);
        logger($durations);

        // Create sequences
        $sequences = collect([
            $this->getPreviewSequence()->toArray(),
            // $this->getCaptionsSequence()->toArray(),
        ]);

        return collect([
            'id' => $this->getRouteKey(),
            'discontinuity' => false,
            'referenceClipIndex' => 1,
            'durations' => $durations,
            'sequences' => $sequences->filter()->values(),
        ]);
    }

    public function getPreviewSequence(): Collection
    {
        return $this->getMedia('clips')?->flatMap(function (Media $media): array {
            return [
                'id' => $media->getRouteKey(),
                'label' => $media->getRouteKey(),
                'clips' => $this->getPreviewClips($media),
            ];
        });
    }

    public function getPreviewClips(Media $media)
    {
        // The last part will be removed
        $parts = $this->getPreviewsParts() + 1;

        $length = $this->getPreviewsLength();

        // VOD service needs Ms
        $msDuration = ($this->duration ?? 10) * 1000;

        // Create equal parts
        $times = Arr::parts($msDuration, $parts);

        array_pop($times);

        logger($times);

        $clips = [];

        foreach ($times as $time) {
            $clips[] = [
                'type' => 'source',
                'path' => $media->getPath(),
                'clipFrom' => $time,
                'clipTo' => $length,
            ];
        }

        return $clips;
    }

    protected function getPreviewsParts(): int
    {
        return config('api.video.previews.parts', 5);
    }

    protected function getPreviewsLength(): int
    {
        return config('api.video.previews.length', 4000);
    }
}
