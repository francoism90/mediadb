<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Services\ImageService;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Video;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class ThumbnailService
{
    public const CONVERSION_NAME = 'thumbnail.jpg';
    public const CONVERSION_TYPE = 'conversions';
    public const THUMBNAIL_FILTER = 'scale=320:180';

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var temporaryDirectory
     */
    protected $temporaryDirectory;

    /**
     * @var ImageService
     */
    protected $imageService;

    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

    public function __construct(
        Filesystem $filesystem,
        TemporaryDirectory $temporaryDirectory,
        ImageService $imageService
    ) {
        $this->filesystem = $filesystem;
        $this->imageService = $imageService;

        $this->temporaryDirectory = $temporaryDirectory::create();

        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 4),
            'ffmpeg.timeout' => 120,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 120,
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
        $framePath = $this->prepareConversion($media);

        // Optimize frame
        $this->imageService->optimize($framePath);

        // Copy to MediaLibrary
        $this->filesystem->copyToMediaLibrary(
            $framePath,
            $media,
            self::CONVERSION_TYPE,
            self::CONVERSION_NAME
        );

        // Mark conversion as done
        $media->markAsConversionGenerated('thumbnail', true);

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
        // Path for the video frame
        $path = $this->temporaryDirectory->path("{$media->id}/thumbnail.jpg");

        // Video instance
        $video = $this->getVideo($media->getPath());

        // Get current media duration
        $duration = $media->getCustomProperty('metadata.duration', 5);

        // Frameshot timecode
        $frameshot = $media->getCustomProperty('frameshot', $duration / 2);

        // Set frame parameters
        $frame = $video->frame(
            TimeCode::fromSeconds($frameshot)
        );

        $frame->addFilter(
            new CustomFrameFilter(self::THUMBNAIL_FILTER)
        );

        // Create frame
        $frame->save($path);

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
}
