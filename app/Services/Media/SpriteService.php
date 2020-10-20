<?php

namespace App\Services\Media;

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
    public const CONVERSION_NAME = 'sprite.webp';
    public const CONVERSION_TYPE = 'conversions';
    public const SPRITE_INTERVAL = 10;
    public const SPRITE_COLUMNS = 20;
    public const SPRITE_WIDTH = 160;
    public const SPRITE_HEIGHT = 90;
    public const SPRITE_FILTER = 'scale=160:190';

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
        $path = $this->temporaryDirectory->path(self::CONVERSION_NAME);

        $imagick = $this->prepareConversion($media);

        // Create montage
        $montage = $imagick->montageImage(
            new ImagickDraw(),
            self::SPRITE_COLUMNS.'x',
            self::SPRITE_WIDTH.'x'.self::SPRITE_HEIGHT.'!',
            Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $montage->writeImage($path);

        // Mark as conversion done
        $media->markAsConversionGenerated('sprite', true);

        // Copy to media-library
        $this->filesystem->copyToMediaLibrary(
            $path,
            $media,
            self::CONVERSION_TYPE,
            self::CONVERSION_NAME
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
                new CustomFrameFilter(self::SPRITE_FILTER)
            );

            $frame->save($framePath);

            // Add image
            $imagick->addImage(
                new Imagick($framePath)
            );

            // Push frame
            $frames->push([
                'id' => $frameCount,
                'start' => $timeCode,
                'end' => $timeCode + self::SPRITE_INTERVAL,
                'x' => $framePositionX,
                'y' => $framePositionY,
            ]);

            // Set next frame positions
            if (0 === $frameCount % self::SPRITE_COLUMNS) {
                $framePositionX = 0;
                $framePositionY += self::SPRITE_HEIGHT;
            } else {
                $framePositionX += self::SPRITE_WIDTH;
            }

            ++$frameCount;
        }

        // Set frames as custom property
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

        return range(0, $duration, self::SPRITE_INTERVAL);
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
