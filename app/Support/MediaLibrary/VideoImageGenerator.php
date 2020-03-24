<?php

namespace App\Support\MediaLibrary;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversions\Conversion;
use Spatie\MediaLibrary\Conversions\ImageGenerators\ImageGenerator;

class VideoImageGenerator extends ImageGenerator
{
    /**
     * @param string     $path
     * @param Conversion $conversion
     *
     * @return string
     */
    public function convert(string $path, Conversion $conversion = null): string
    {
        $imagePath = pathinfo($path, PATHINFO_DIRNAME).'/'.pathinfo($path, PATHINFO_FILENAME).'.jpg';

        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($path);
        $duration = $ffmpeg->getFFProbe()->format($path)->get('duration');

        $seconds = $conversion ? $conversion->getExtractVideoFrameAtSecond() : 0;
        $seconds = $duration < $seconds ? 0 : $seconds;

        $frame = $video->frame(TimeCode::fromSeconds($seconds));
        $frame->save($imagePath);

        return $imagePath;
    }

    /**
     * @return bool
     */
    public function requirementsAreInstalled(): bool
    {
        return class_exists('\\FFMpeg\\FFMpeg');
    }

    /**
     * @return Collection
     */
    public function supportedExtensions(): Collection
    {
        return collect([
            'm4v',
            'mp4',
            'ogm',
            'ogv',
            'ogx',
            'vp8',
            'vp9',
            'webm',
        ]);
    }

    /**
     * @return Collection
     */
    public function supportedMimeTypes(): Collection
    {
        return collect([
            'video/mp4',
            'video/ogg',
            'video/vp8',
            'video/vp9',
            'video/webm',
            'video/x-m4v',
        ]);
    }
}
