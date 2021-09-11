<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use Illuminate\Support\Collection;

class UpdateMetadataDetails
{
    public function __invoke(Media $media)
    {
        $metadata = $this->getMetadata($media);

        $this->setMetadataPropery($media, $metadata);
    }

    protected function getMetadata(Media $media): Collection
    {
        $collect = collect();

        if ('video' === $media->type) {
            $collect = $collect->merge($this->getFormatAttributes($media));
            $collect = $collect->merge($this->getVideoAttributes($media));
        }

        return $collect;
    }

    protected function setMetadataPropery(Media $media, Collection $metadata): void
    {
        $metadata->each(fn ($value, $key) => $media->setCustomProperty($key, $value));

        $media->save();
    }

    protected function getFormatAttributes(Media $media): Collection
    {
        $format = app(FFMpegService::class)->getFormat($media->getPath());

        return collect([
            'bitrate' => $format->get('bit_rate', 0),
            'duration' => $format->get('duration', 0),
            'probe_score' => $format->get('probe_score', 0),
            'start_time' => $format->get('start_time', 0),
        ]);
    }

    protected function getVideoAttributes(Media $media): Collection
    {
        $stream = app(FFMpegService::class)->getVideoStreams($media->getPath())->first();

        return collect([
            'closed_captions' => $stream->get('closed_captions', null),
            'codec_name' => $stream->get('codec_name', null),
            'coded_height' => $stream->get('coded_height', 0),
            'coded_width' => $stream->get('coded_width', 0),
            'display_aspect_ratio' => $stream->get('display_aspect_ratio', null),
            'height' => $stream->get('height', 0),
            'width' => $stream->get('width', 0),
            'pix_fmt' => $stream->get('pix_fmt', 0),
            'profile' => $stream->get('profile', null),
        ]);
    }
}
