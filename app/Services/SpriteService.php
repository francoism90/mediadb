<?php

namespace App\Services;

use App\Models\Media;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Video;
use Imagick;
use ImagickDraw;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class SpriteService
{
    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var temporaryDirectory
     */
    protected $temporaryDirectory;

    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

    public function __construct(
        Filesystem $filesystem,
        TemporaryDirectory $temporaryDirectory
    ) {
        $this->filesystem = $filesystem;
        $this->temporaryDirectory = $temporaryDirectory::create();

        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 0),
            'ffmpeg.timeout' => 2700,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 2700,
        ]);
    }

    public function __destruct()
    {
        $this->temporaryDirectory->delete();
    }

    /**
     * @param Media $media
     *
     * @return void
     */
    public function create(Media $media): void
    {
        $path = $this->temporaryDirectory->path(config('video.sprite_name'));

        $imagick = $this->prepareConversion($media);

        $montage = $imagick->montageImage(
            new ImagickDraw(),
            config('video.sprite_columns').'x',
            config('video.sprite_width').'x'.config('video.sprite_height').'!',
            Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $montage->writeImage($path);

        $media->markAsConversionGenerated('sprite');

        $this->filesystem->copyToMediaLibrary(
            $path,
            $media,
            'conversions',
            config('video.sprite_name')
        );
    }

    /**
     * @param Media $media
     *
     * @return Imagick
     */
    protected function prepareConversion(Media $media): Imagick
    {
        $video = $this->getVideo($media->getPath());

        $timeCodes = $this->getFrameRange($media);

        $frames = collect();

        $frameCount = 1;
        $framePositionX = 0;
        $framePositionY = 0;

        $imagick = new Imagick();

        foreach ($timeCodes as $timeCode) {
            $framePath = $this->temporaryDirectory->path("frames/{$frameCount}.jpg");

            $frame = $video->frame(
                TimeCode::fromSeconds($timeCode)
            );

            $frame->addFilter(
                new CustomFrameFilter(config('video.sprite_filter'))
            );

            $frame->save($framePath);

            $imagick->addImage(
                new Imagick($framePath)
            );

            $frames->push([
                'id' => $frameCount,
                'start' => $timeCode,
                'end' => $timeCode + config('video.sprite_interval'),
                'x' => $framePositionX,
                'y' => $framePositionY,
            ]);

            // Set next frame positions
            if (0 === $frameCount % config('video.sprite_columns')) {
                $framePositionX = 0;
                $framePositionY += config('video.sprite_height');
            } else {
                $framePositionX += config('video.sprite_width');
            }

            ++$frameCount;
        }

        $media->setCustomProperty('sprite', $frames);

        return $imagick;
    }

    /**
     * @param Media $media
     *
     * @return array
     */
    protected function getFrameRange(Media $media): array
    {
        $duration = $media->getCustomProperty('metadata.duration', 0);

        return range(0, $duration, config('video.sprite_interval'));
    }

    /**
     * @param string $path
     *
     * @return Video
     */
    protected function getVideo(string $path): Video
    {
        return $this->ffmpeg->open($path);
    }
}
