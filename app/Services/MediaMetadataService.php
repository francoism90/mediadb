<?php

namespace App\Services;

use Illuminate\Support\Collection;

class MediaMetadataService
{
    public function __construct(
        protected FFMpegService $ffmpegService,
    ) {
        //
    }

    /**
     * @param string $path
     *
     * @return Collection
     */
    public function getFormatAttributes(string $path): Collection
    {
        $format = $this->ffmpegService->getFileFormat($path);

        return collect([
            'start_time' => $format->get('start_time', 0),
            'duration' => $format->get('duration', 0),
            'size' => $format->get('size', 0),
            'bitrate' => $format->get('bit_rate', 0),
            'probe_score' => $format->get('probe_score', 0),
        ]);
    }

    /**
     * @param string $path
     *
     * @return Collection
     */
    public function getVideoAttributes(string $path): Collection
    {
        $stream = $this->ffmpegService->getVideoStreams($path)->first();

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
}
