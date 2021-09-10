<?php

namespace App\Services;

use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\Media\Video;
use RuntimeException;

class FFMpegService
{
    protected FFMpeg $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);
    }

    public function isValid(string $path): bool
    {
        return $this->ffmpeg->getFFProbe()->isValid($path);
    }

    public function open(string $path): Video
    {
        return $this->ffmpeg->open($path);
    }

    public function getFormat(string $path): Format
    {
        throw_if(!$this->isValid($path), RuntimeException::class);

        return $this->ffmpeg->getFFProbe()->format($path);
    }

    public function getVideoStreams(string $path): StreamCollection
    {
        throw_if(!$this->isValid($path), RuntimeException::class);

        return $this
            ->ffmpeg
            ->getFFProbe()
            ->streams($path)
            ->videos();
    }
}
