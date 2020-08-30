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
    public const CONVERSION_NAME = 'sprite.json';
    public const CONVERSION_TYPE = 'conversions';
    public const SPRITE_ROWS = 8;
    public const SPRITE_COLUMNS = 8;
    public const SPRITE_ITEMS = 64;
    public const SPRITE_INTERVAL = 2;
    public const SPRITE_FILTER = 'scale=160:90';
    public const SPRITE_WIDTH = 160;
    public const SPRITE_HEIGHT = 90;

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
        // Perform conversion
        $spritePath = $this->prepareConversion($media);

        // Copy to MediaLibrary
        $this->filesystem->copyToMediaLibrary(
            $spritePath,
            $media,
            self::CONVERSION_TYPE,
            self::CONVERSION_NAME
        );

        // Mark conversion as done
        $media->markAsConversionGenerated('sprite', true);

        // Delete temporary files
        $this->temporaryDirectory->delete();
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    protected function prepareConversion(Media $media): string
    {
        // Create video frames
        $videoFrames = $this->createFrames($media);

        // Create video sprites
        $videoSprites = $this->createSprites($videoFrames);

        // Copy sprites
        $spriteGroups = $videoSprites->groupBy('sprite')->toArray();

        foreach ($spriteGroups as $key => $sprites) {
            $this->filesystem->copyToMediaLibrary(
                $this->temporaryDirectory->path("sprite-${key}.webp"),
                $media,
                self::CONVERSION_TYPE,
                "sprite-${key}.webp"
            );
        }

        // Create sprite json
        $path = $this->createSpriteJson($videoSprites);

        return $path;
    }

    /**
     * @param Media $media
     *
     * @return Collection
     */
    protected function createFrames(Media $media): Collection
    {
        // Video instance
        $video = $this->getVideo($media->getPath());

        // Media duration
        $duration = $media->getCustomProperty('metadata.duration', 10);

        // Frameshot collection
        $parts = $this->getFrameRanges($duration);

        // Frame generation
        $frames = collect();

        foreach ($parts as $part) {
            $path = $this->temporaryDirectory->path("frames/{$part}.png");

            // Create video frame
            $frame = $video->frame(
                TimeCode::fromSeconds($part)
            );

            $frame->addFilter(
                new CustomFrameFilter(self::SPRITE_FILTER)
            );

            $frame->save($path);

            // Add frame to collection
            $frames->push([
                'path' => $path,
                'start' => gmdate('H:i:s.v', $part),
                'end' => gmdate('H:i:s.v', $part + self::SPRITE_INTERVAL),
            ]);
        }

        return $frames;
    }

    /**
     * @param Collection $frames
     *
     * @return Collection
     */
    protected function createSprites(Collection $frames): Collection
    {
        // Calculate sprite sections
        $frameCount = 1;
        $spriteCount = 1;

        // Calculate sprite frame positions
        $spriteFrameX = 0;
        $spriteFrameY = 0;

        $spriteFrames = collect();

        foreach ($frames as $frame) {
            // Set frame attributes
            $frame['sprite'] = $spriteCount;
            $frame['x'] = $spriteFrameX;
            $frame['y'] = $spriteFrameY;

            $spriteFrames->push($frame);

            // Set next frame positions
            if (0 === $frameCount % self::SPRITE_COLUMNS) {
                $spriteFrameX = 0;
                $spriteFrameY += self::SPRITE_HEIGHT;
            } else {
                $spriteFrameX += self::SPRITE_WIDTH;
            }

            // Create new section (if needed)
            if (0 === $frameCount % self::SPRITE_ITEMS || $frames->last()['start'] === $frame['start']) {
                $this->createMontage(
                    $spriteCount,
                    $spriteFrames->where('sprite', $spriteCount)
                );

                // Reset Counters
                $frameCount = 1;

                // Reset thumbnail calculations
                $spriteFrameX = 0;
                $spriteFrameY = 0;

                ++$spriteCount;
                continue;
            }

            ++$frameCount;
        }

        return $spriteFrames;
    }

    /**
     * @param int        $sprite
     * @param Collection $frames
     *
     * @return void
     */
    protected function createMontage(int $sprite, Collection $frames): void
    {
        $path = $this->temporaryDirectory->path("sprite-{$sprite}.webp");

        // Init imagick
        $imagick = new \Imagick();

        foreach ($frames as $frame) {
            // Create missing frame (if needed)
            if (!file_exists($frame['path'])) {
                $image = new \Imagick();

                $image->newImage(
                    self::SPRITE_WIDTH,
                    self::SPRITE_HEIGHT,
                    new \ImagickPixel('black')
                );

                $image->setImageFormat('png');

                $imagick->addImage($image);
                continue;
            }

            $imagick->addImage(
                new \Imagick($frame['path'])
            );
        }

        $sprite = $imagick->montageImage(
            new \ImagickDraw(),
            self::SPRITE_COLUMNS.'x',
            self::SPRITE_WIDTH.'x'.self::SPRITE_HEIGHT.'!',
            \Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $sprite->writeImage($path);
    }

    /**
     * @param Collection $frames
     *
     * @return string
     */
    protected function createSpriteJson(Collection $frames): string
    {
        $path = $this->temporaryDirectory->path('sprite.json');

        // Remove the temporary path
        $filtered = $frames->map(function ($item) {
            unset($item['path']);

            return $item;
        });

        // Write the sprite json
        file_put_contents($path, json_encode(
            $filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        return $path;
    }

    /**
     * @param float $duration
     *
     * @return array
     */
    protected function getFrameRanges(float $duration): array
    {
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
