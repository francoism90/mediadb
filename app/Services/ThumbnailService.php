<?php

namespace App\Services;

use App\Models\Media;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
use FFMpeg\Media\Video;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class ThumbnailService
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
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 0),
            'ffmpeg.timeout' => 300,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 300,
        ]);
    }

    /**
     * @param Media $media
     *
     * @return void
     */
    public function create(Media $media): void
    {
        $framePath = $this->prepareConversion($media);

        $this->imageService->optimize($framePath);

        $this->filesystem->copyToMediaLibrary(
            $framePath,
            $media,
            'conversions',
            config('video.thumbnail_name', 'thumbnail.webp')
        );

        $media->markAsConversionGenerated('thumbnail');
    }

    public function __destruct()
    {
        $this->temporaryDirectory->delete();
    }

    /**
     * @param Media $media
     *
     * @return string
     */
    protected function prepareConversion(Media $media): string
    {
        $path = $this->temporaryDirectory->path("{$media->id}/thumbnail.jpg");

        $video = $this->getVideo($media->getPath());

        $duration = $media->getCustomProperty('metadata.duration', 60);
        $frameshot = $media->getCustomProperty('frameshot', $duration / 2);

        $frame = $video->frame(
            TimeCode::fromSeconds($frameshot)
        );

        $frame->addFilter(
            new CustomFrameFilter(config('video.thumbnail_filter'))
        );

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
