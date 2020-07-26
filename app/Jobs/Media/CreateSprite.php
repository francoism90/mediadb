<?php

namespace App\Jobs\Media;

use App\Models\Media;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class CreateSprite implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var int
     */
    public $tries = 1;

    /**
     * @var int
     */
    public $timeout = 2700;

    /**
     * @var Media
     */
    protected $media;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media->fresh()->withoutRelations();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'timeout' => $this->timeout,
            'ffmpeg.threads' => config('media-library.threads', 0),
        ]);

        $video = $ffmpeg->open($this->media->getPath());

        /* @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        // Prepare the conversion
        $tempDirectory = TemporaryDirectory::create();

        $spriteFrames = $this->performConversion($tempDirectory, $video);

        // Copy sprite json
        $spritePath = $tempDirectory->path('sprite.json');

        $this->createSpriteJson($spritePath, $spriteFrames);

        $filesystem->copyToMediaLibrary(
            $spritePath,
            $this->media,
            'conversions',
            'sprite.json'
        );

        // Copy sprites
        $spriteGroups = $spriteFrames->groupBy('sprite')->toArray();

        foreach ($spriteGroups as $key => $sprites) {
            $filesystem->copyToMediaLibrary(
                $tempDirectory->path("sprite-${key}.webp"),
                $this->media,
                'conversions',
                "sprite-${key}.webp"
            );
        }

        $this->media->markAsConversionGenerated('sprite', true);

        $tempDirectory->delete();
    }

    /**
     * @param TemporaryDirectory $tempDirectory
     * @param Video              $video
     *
     * @return Collection
     */
    protected function performConversion($tempDirectory, Video $video): Collection
    {
        // Sprite second ranges (e.g. 2.0, 4.0, 5.0 ..)
        $parts = $this->getSpriteRanges();

        // Thumbnail size
        $thumbWidth = config('vod.sprite.width', 160);
        $thumbHeight = config('vod.sprite.height', 90);

        // Sprite parameters
        $spriteInterval = config('vod.sprite.interval', 2);
        $spriteRows = config('vod.sprite.rows', 8);
        $spriteColumns = config('vod.sprite.columns', 8);
        $spriteSplit = $spriteRows * $spriteColumns;

        // Sprite generation
        $frames = collect();

        $spriteFrameX = 0;
        $spriteFrameY = 0;

        $spriteCount = 1;
        $frameCount = 1;

        foreach ($parts as $part) {
            // Create frame image
            $path = $tempDirectory->path("frames/{$spriteCount}/{$part}.png");

            $frame = $video->frame(TimeCode::fromSeconds($part));

            $frame->addFilter(
                new CustomFrameFilter("scale={$thumbWidth}:{$thumbHeight}")
            );

            $frame->save($path);

            // Add frame
            $frames->push([
                'id' => $frameCount,
                'sprite' => $spriteCount,
                'path' => $path,
                'start' => gmdate('H:i:s.v', $part),
                'end' => gmdate('H:i:s.v', $part + $spriteInterval),
                'x' => $spriteFrameX,
                'y' => $spriteFrameY,
            ]);

            // Calculate frame position
            if (0 === $frameCount % $spriteColumns) {
                $spriteFrameX = 0;
                $spriteFrameY += $thumbHeight;
            } else {
                $spriteFrameX += $thumbWidth;
            }

            // Create sprite if limit reached
            if (0 === $frameCount % $spriteSplit || $parts->last() === $part) {
                $spritePath = $tempDirectory->path("sprite-{$spriteCount}.webp");

                $this->createSpriteMontage(
                    $spritePath, $frames->where('sprite', $spriteCount)
                );

                // Reset Counters
                $frameCount = 1;

                // Reset thumbnail calculations
                $spriteFrameX = 0;
                $spriteFrameY = 0;

                ++$spriteCount;
                continue;
            }

            // Increase counters
            ++$frameCount;
        }

        return $frames;
    }

    /**
     * @param string     $path
     * @param Collection $frames
     *
     * @return self
     */
    protected function createSpriteMontage(string $path, Collection $frames): self
    {
        $imagick = new \Imagick();

        // Sprite parameters
        $spriteColumns = config('vod.sprite.columns', 10);

        $thumbWidth = config('vod.sprite.width', 160);
        $thumbHeight = config('vod.sprite.height', 90);

        // Add each frame for montage
        foreach ($frames as $frame) {
            // Create empty frame if non exists
            if (!file_exists($frame['path'])) {
                $image = new \Imagick();
                $image->newImage($thumbWidth, $thumbHeight, new \ImagickPixel('black'));
                $image->setImageFormat('png');

                $imagick->addImage($image);
                continue;
            }

            $imagick->addImage(
                new \Imagick($frame['path'])
            );
        }

        // Create montage
        $sprite = $imagick->montageImage(
            new \ImagickDraw(),
            "{$spriteColumns}x",
            "{$thumbWidth}x{$thumbHeight}!",
            \Imagick::MONTAGEMODE_CONCATENATE,
            '0'
        );

        $sprite->writeImage($path);

        return $this;
    }

    /**
     * @param string     $path
     * @param Collection $frames
     *
     * @return self
     */
    protected function createSpriteJson(string $path, Collection $frames): self
    {
        // Remove temporary path
        $filtered = $frames->map(function ($item) {
            unset($item['path']);

            return $item;
        });

        file_put_contents($path, json_encode(
            $filtered, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ));

        return $this;
    }

    /**
     * @return Collection
     */
    protected function getSpriteRanges(): Collection
    {
        $ranges = range(
            0,
            floor($this->media->getCustomProperty('duration', 0)),
            config('vod.sprite.interval', 1)
        );

        return collect($ranges);
    }
}
