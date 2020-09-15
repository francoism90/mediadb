<?php

namespace App\Services\Media;

use App\Models\Media;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Video;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class SpriteService
{
    public const CONVERSION_NAME = 'sprite.webp';
    public const CONVERSION_TYPE = 'conversions';
    public const SPRITE_ITEMS = 20;
    public const SPRITE_COLUMNS = 5;
    public const SPRITE_WIDTH = 220;
    public const SPRITE_HEIGHT = 160;
    public const SPRITE_FILTER = 'scale=220:160';

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
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 4),
            'ffmpeg.timeout' => 2700,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 2700,
        ]);
    }

    /**
     * @param Media $name
     *
     * @return void
     */
    public function create(Media $media): void
    {
        $spritePath = $this->prepareConversion($media);

        $this->filesystem->copyToMediaLibrary(
            $spritePath,
            $media,
            self::CONVERSION_TYPE,
            self::CONVERSION_NAME
        );

        $media->markAsConversionGenerated('sprite', true);

        $this->temporaryDirectory->delete();
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    protected function prepareConversion(Media $media): string
    {
        // Create each frame
        $frames = $this->createFrames($media);

        // Create sprite montage
        $path = $this->createMontage($frames);

        // Set as media property
        $filtered = $frames->map(function ($item) {
            unset($item['path']);

            return $item;
        });

        $media->setCustomProperty('sprite', $filtered);

        return $path;
    }

    /**
     * @param Media $media
     *
     * @return Collection
     */
    protected function createFrames(Media $media): Collection
    {
        // Media duration
        $duration = $media->getCustomProperty('metadata.duration', 60);

        // Calculate frame ranges
        $parts = $this->getFramesByRange($duration);

        // Video instance
        $video = $this->getVideo($media->getPath());

        // Create frames
        $frames = collect();

        $frameCount = 1;
        $framePositionX = 0;
        $framePositionY = 0;

        foreach ($parts as $part) {
            $path = $this->temporaryDirectory->path("frames/{$frameCount}.png");

            $frame = $video->frame(
                TimeCode::fromSeconds($part)
            );

            $frame->addFilter(
                new CustomFrameFilter(self::SPRITE_FILTER)
            );

            $frame->save($path);

            // Add to collection
            $frames->push([
                'id' => $frameCount,
                'timecode' => $part,
                'x' => $framePositionX,
                'y' => $framePositionY,
                'path' => $path,
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

        return $frames;
    }

    /**
     * @param Collection $frames
     *
     * @return string
     */
    protected function createMontage(Collection $frames): string
    {
        $path = $this->temporaryDirectory->path('sprite.webp');

        $imagick = new \Imagick();

        foreach ($frames as $frame) {
            $imagick->addImage(
                new \Imagick($frame['path'])
            );
        }

        $montage = $imagick->montageImage(
            new \ImagickDraw(),
            self::SPRITE_COLUMNS.'x',
            self::SPRITE_WIDTH.'x'.self::SPRITE_HEIGHT.'!',
            \Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $montage->writeImage($path);

        return $path;
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

    /**
     * @param float $duration
     *
     * @return array
     */
    protected function getFramesByRange(float $duration): array
    {
        $step = $duration / (self::SPRITE_ITEMS + 1);

        $range = array_map('round', range(0, $duration, $step));

        // Remove first and last parts
        $frames = array_slice($range, 1, -1);

        return $frames;
    }
}
