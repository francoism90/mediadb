<?php

namespace App\Actions\Media;

use App\Models\Media;
use App\Services\FFMpegService;
use Illuminate\Support\Collection;
use Spatie\QueueableAction\QueueableAction;

class UpdateMetadataDetails
{
    use QueueableAction;

    public function __construct(
        protected FFMpegService $ffmpegService,
    ) {
    }

    public function execute(Media $media): void
    {
        $metadata = $this->gatherMetadata($media);

        $this->setMetadataPropery($media, $metadata);
    }

    private function gatherMetadata(Media $media): Collection
    {
        $type = $this->getType($media);

        $collect = collect();

        if ('video' === $type) {
            $collect = $collect->merge($this->getFormatAttributes($media));
            $collect = $collect->merge($this->getVideoAttributes($media));
        }

        return $collect;
    }

    private function setMetadataPropery(Media $media, Collection $metadata): void
    {
        $media->setCustomProperty('metadata', $metadata->all())->save();
    }

    private function getFormatAttributes(Media $media): Collection
    {
        $format = $this->ffmpegService->getFormat($media->getPath());

        return collect([
            'start_time' => $format->get('start_time', 0),
            'duration' => $format->get('duration', 0),
            'size' => $format->get('size', 0),
            'bitrate' => $format->get('bit_rate', 0),
            'probe_score' => $format->get('probe_score', 0),
        ]);
    }

    private function getVideoAttributes(Media $media): Collection
    {
        $stream = $this->ffmpegService->getVideoStreams($media->getPath())->first();

        return collect([
            'codec_name' => $stream->get('codec_name', null),
            'profile' => $stream->get('profile', null),
            'width' => $stream->get('width', 0),
            'height' => $stream->get('height', 0),
            'coded_width' => $stream->get('coded_width', 0),
            'coded_height' => $stream->get('coded_height', 0),
            'closed_captions' => $stream->get('closed_captions', null),
            'pix_fmt' => $stream->get('pix_fmt', 0),
            'display_aspect_ratio' => $stream->get('display_aspect_ratio', null),
        ]);
    }

    private function getType(Media $media): string
    {
        return strtok($media->mime_type, '/');
    }
}
