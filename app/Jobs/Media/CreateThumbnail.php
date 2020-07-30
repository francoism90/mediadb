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
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

class CreateThumbnail implements ShouldQueue
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
    public $tries = 3;

    /**
     * @var int
     */
    public $timeout = 600;

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
            'ffmpeg.threads' => config('media-library.threads', 4),
            'ffmpeg.timeout' => $this->timeout,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => $this->timeout,
        ]);

        $video = $ffmpeg->open($this->media->getPath());

        // Prepare the conversion
        $tempDirectory = TemporaryDirectory::create();

        $path = $this->prepareConversion($tempDirectory, $video);

        /** @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        $filesystem->copyToMediaLibrary(
            $path,
            $this->media,
            'conversions',
            $this->getConversionFileName()
        );

        $this->media->markAsConversionGenerated('thumbnail', true);

        $tempDirectory->delete();
    }

    /**
     * @param TemporaryDirectory $tempDirectory
     * @param Video              $video
     *
     * @return string
     */
    protected function prepareConversion($tempDirectory, Video $video): string
    {
        $path = $tempDirectory->path($this->getConversionFileName());

        $duration = $this->media->getCustomProperty('duration', 5);

        $frame = $video->frame(TimeCode::fromSeconds(
            $this->media->getCustomProperty('snapshot', $duration / 2)
        ));

        $frame->addFilter(
            new CustomFrameFilter('scale=320:240')
        );

        $frame->save($path);

        // Optimize the conversion
        $optimizer = OptimizerChainFactory::create();
        $optimizer->optimize($path);

        return $path;
    }

    /**
     * @return string
     */
    protected function getConversionFileName(): string
    {
        return 'thumbnail.jpg';
    }
}
