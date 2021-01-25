<?php

namespace App\Services;

use App\Exceptions\InvalidFileException;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\StreamCollection;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Audio;
use FFMpeg\Media\Video;

class FFMpegService
{
    /**
     * @var FFMpeg
     */
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

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isValidFile(string $path): bool
    {
        return $this
            ->ffmpeg
            ->getFFProbe()
            ->isValid($path);
    }

    /**
     * @param string $path
     *
     * @return Audio|Video
     */
    public function openFile(string $path)
    {
        return $this->ffmpeg->open($path);
    }

    /**
     * @param string $path
     *
     * @return Format
     */
    public function getFileFormat(string $path): Format
    {
        throw_if(!$this->isValidFile($path), InvalidFileException::class);

        return $this->ffmpeg->getFFProbe()->format($path);
    }

    /**
     * @param string $path
     *
     * @return StreamCollection
     */
    public function getVideoStreams(string $path): StreamCollection
    {
        throw_if(!$this->isValidFile($path), InvalidFileException::class);

        return $this
            ->ffmpeg
            ->getFFProbe()
            ->streams($path)
            ->videos();
    }

    /**
     * @param string $path
     * @param float  $timeCode
     * @param string $filter
     *
     * @return string
     */
    public function createThumbnail(
        Video $video,
        string $path,
        float $timeCode = 0,
        string $filter = ''
    ): string {
        $frame = $video->frame(
            TimeCode::fromSeconds($timeCode)
        );

        $frame->addFilter(
            new CustomFrameFilter($filter)
        );

        $frame->save($path);

        return $path;
    }
}
