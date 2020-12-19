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
            'ffmpeg.timeout' => 5400,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 5400,
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
        $imagick = $this->prepareConversion($media);

        $spritePath = $this->createSprite($imagick);

        $this->filesystem->copyToMediaLibrary(
            $spritePath,
            $media,
            'conversions',
            config('video.sprite_name')
        );

        $media->markAsConversionGenerated('sprite');
    }

    /**
     * @param Media $media
     *
     * @return Imagick
     */
    protected function prepareConversion(Media $media): Imagick
    {
        $imagick = new Imagick();

        $video = $this->getVideoProperties($media);

        $frames = collect();

        $frameCount = 1;
        $framePositionX = 0;
        $framePositionY = 0;

        $timeCodes = $this->getFrameRange($media);

        foreach ($timeCodes as $timeCode) {
            $framePath = $this->createFrame($video, $timeCode);

            $imagick->addImage(
                new Imagick($framePath)
            );

            // Push to the collection
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
     * @param Video $video
     * @param float $seconds
     *
     * @return string
     */
    protected function createFrame(Video $video, float $seconds = 0): string
    {
        $path = $this->temporaryDirectory->path("frames/{$seconds}.jpg");

        $frame = $video->frame(
            TimeCode::fromSeconds($seconds)
        );

        $frame->addFilter(
            new CustomFrameFilter(config('video.sprite_filter'))
        );

        $frame->save($path);

        return $path;
    }

    /**
     * @param Imagick $imagick
     *
     * @return string
     */
    protected function createSprite(Imagick $imagick): string
    {
        $path = $this->temporaryDirectory->path(config('video.sprite_name'));

        $montage = $imagick->montageImage(
            new ImagickDraw(),
            config('video.sprite_columns').'x',
            config('video.sprite_width').'x'.config('video.sprite_height').'!',
            Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $montage->writeImage($path);

        return $path;
    }

    /**
     * @param Media $media
     *
     * @return array
     */
    protected function getFrameRange(Media $media): array
    {
        $duration = $media->getCustomProperty('metadata.duration', 60);

        return range(0, $duration, config('video.sprite_interval'));
    }

    /**
     * @param Media $media
     *
     * @return Video
     */
    protected function getVideoProperties(Media $media): Video
    {
        return $this->ffmpeg->open($media->getPath());
    }
}
