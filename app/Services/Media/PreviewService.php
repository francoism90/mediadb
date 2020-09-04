<?php

namespace App\Services\Media;

use App\Models\Media;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\FrameRate;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class PreviewService
{
    public const CONVERSION_NAME = 'preview.mp4';
    public const CONVERSION_TYPE = 'conversions';
    public const PREVIEW_BITRATE = 200;
    public const PREVIEW_FRAMERATE = 30;
    public const PREVIEW_GOP = 60;
    public const PREVIEW_PARTS = [1, 3, 5, 7, 9, 11, 13, 15];
    public const PREVIEW_SECONDS = 1.25;
    public const PREVIEW_WIDTH = 320;
    public const PREVIEW_HEIGHT = 180;
    public const PREVIEW_DIVIDER = 16;

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
            'ffmpeg.timeout' => 300,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 300,
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
        $previewPath = $this->prepareConversion($media);

        // Copy to MediaLibrary
        $this->filesystem->copyToMediaLibrary(
            $previewPath,
            $media,
            self::CONVERSION_TYPE,
            self::CONVERSION_NAME
        );

        // Mark conversion as done
        $media->markAsConversionGenerated('preview', true);

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
        // Create video clips
        $videoClips = $this->createClips($media);

        // Conversion path
        $path = $this->temporaryDirectory->path("{$media->id}/preview.mp4");

        // We need to load the first video
        $video = $this->getVideo($videoClips[0]);

        // Video concatenation
        $video
            ->concat($videoClips)
            ->saveFromSameCodecs($path, true);

        return $path;
    }

    /**
     * @param Media $media
     *
     * @return array
     */
    protected function createClips(Media $media): array
    {
        // Media video
        $video = $this->getVideo($media->getPath());

        // Media duration
        $duration = $media->getCustomProperty('metadata.duration', 10);

        // Frameshot ranges
        $frameRanges = $this->getFrameRanges($duration);

        // Clip format
        $format = new X264();
        $format->setKiloBitrate(self::PREVIEW_BITRATE);

        // Keep each clip
        $clips = [];

        foreach (self::PREVIEW_PARTS as $part) {
            $path = $this->temporaryDirectory->path("{$media->id}/{$part}.mp4");

            $clip = $video->clip(
                TimeCode::fromSeconds($frameRanges[$part]),
                TimeCode::fromSeconds(self::PREVIEW_SECONDS)
            );

            $clip->addFilter(new SimpleFilter(['-an'])) // disable audio codec)
                 ->filters()
                 ->resize(
                     new Dimension(self::PREVIEW_WIDTH, self::PREVIEW_HEIGHT),
                     ResizeFilter::RESIZEMODE_FIT,
                     false
                 )
                ->framerate(
                    new FrameRate(self::PREVIEW_FRAMERATE),
                    self::PREVIEW_GOP
                );

            $clip->save($format, $path);

            $clips[] = $path;
        }

        return $clips;
    }

    /**
     * @param float $duration
     *
     * @return array
     */
    protected function getFrameRanges(float $duration): array
    {
        $steps = $duration / self::PREVIEW_DIVIDER;

        return range(0, $duration, $steps);
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
