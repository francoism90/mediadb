<?php

namespace App\Jobs\Media;

use App\Models\Media;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Frame\CustomFrameFilter;
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
    public $tries = 1;

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
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($this->media->getPath());

        // Prepare the conversion
        $tempDirectory = TemporaryDirectory::create();

        $path = $tempDirectory->path($this->getConversionFileName());

        $frame = $video->frame(TimeCode::fromSeconds(
            $this->media->getCustomProperty('snapshot', 1)
        ));

        $frame->addFilter(
            new CustomFrameFilter("scale='min(320,iw)':'min(240,ih)")
        );

        $frame->save($path);

        // Optimize the conversion
        $optimizer = OptimizerChainFactory::create();

        $optimizer->optimize($path);

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
     * @return string
     */
    protected function getConversionFileName(): string
    {
        $filename = pathinfo($this->media->file_name, PATHINFO_FILENAME);

        return "{$filename}-thumbnail.jpg";
    }
}
