<?php

namespace App\Services;

use App\Exceptions\InvalidFileException;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;

class FFMpegService
{
    protected FFMpeg $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 0),
            'ffmpeg.timeout' => config('media-library.ffmpeg_timeout', 5400),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 120),
            'timeout' => config('media-library.timeout', 5400),
        ]);
    }

    public function isValid(string $path): bool
    {
        return $this->ffmpeg->getFFProbe()->isValid($path);
    }

    public function open(string $path): Audio | Video
    {
        return $this->ffmpeg->open($path);
    }

    public function getFormat(string $path): Format
    {
        throw_if(!$this->isValid($path), InvalidFileException::class);

        return $this->ffmpeg->getFFProbe()->format($path);
    }

    public function getVideoStreams(string $path): StreamCollection
    {
        throw_if(!$this->isValid($path), InvalidFileException::class);

        return $this
            ->ffmpeg
            ->getFFProbe()
            ->streams($path)
            ->videos();
    }
}
