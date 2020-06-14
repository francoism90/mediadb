<?php

namespace App\Jobs\Media;

use App\Models\Media;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class CreatePreview implements ShouldQueue
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
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 120;

    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @var TemporaryDirectory
     */
    protected $tmp;

    /**
     * @var array
     */
    protected $parts;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
                'ffprobe.binaries' => config('media-library.ffprobe_path'),
            ]);

        $this->tmp = (new TemporaryDirectory())->create();

        $this->prepareVideoClips();
        $this->performConversion();

        $this->tmp->delete();
    }

    /**
     * @return self
     */
    protected function prepareVideoClips(): self
    {
        $ranges = $this->getClipsRanges();

        $parts = $this->media->getCustomProperty('clips', [1, 3, 5, 7]);

        foreach ($parts as $part) {
            $path = $this->tmp->path("{$part}.mp4");

            $format = new X264();
            $format->setKiloBitrate(200);

            $clip = $this->getVideo()->clip(
                TimeCode::fromSeconds($ranges[$part]), TimeCode::fromSeconds(2)
            );

            $clip->addFilter(new SimpleFilter(['-an'])) // disable audio codec
                 ->filters()
                 ->resize(new Dimension(320, 240), ResizeFilter::RESIZEMODE_INSET, true);

            $clip->save($format, $path);

            $this->parts[] = $path;
        }

        return $this;
    }

    /**
     * @return void
     */
    protected function performConversion(): void
    {
        $video = $this->getVideo();

        $video->concat($this->parts)
              ->saveFromSameCodecs($this->tmp->path('preview.mp4'), true);

        copy(
            $this->tmp->path('preview.mp4'),
            $this->getRootPath().'/conversions/'.$this->getFinalFileName()
        );

        $this->media->markAsConversionGenerated('preview', true);
    }

    /**
     * @return string
     */
    protected function getRootPath(): string
    {
        return dirname($this->media->getPath());
    }

    /**
     * @return string
     */
    protected function getFinalFileName(): string
    {
        return pathinfo($this->media->file_name, PATHINFO_FILENAME).'-preview.mp4';
    }

    /**
     * @return array
     */
    protected function getClipsRanges(): array
    {
        $duration = $this->media->getCustomProperty('duration', 10);
        $steps = $duration / $this->media->getCustomProperty('divider', 8);

        return range(0, $duration, $steps);
    }

    /**
     * @return Video
     */
    protected function getVideo(): Video
    {
        return $this->ffmpeg->open($this->media->getPath());
    }
}
