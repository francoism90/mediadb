<?php

namespace App\Listeners\Media;

use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Spatie\MediaLibrary\Conversions\Events\ConversionHasBeenCompleted;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class CreatePreviewClip implements ShouldQueue
{
    use InteractsWithQueue;

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
    protected $collection;

    /**
     * @var int
     */
    public $timeout = 240;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);
    }

    /**
     * Execute the job.
     */
    public function handle(ConversionHasBeenCompleted $event)
    {
        $this->media = $event->media;

        if (!$this->hasValidMime('video')) {
            throw new \Exception('Invalid mimetype given.');
        }

        $hasPreview = $this->media->hasGeneratedConversion('preview');

        if (!$hasPreview) {
            $this->tmp = (new TemporaryDirectory())->create();

            $this->createPreviewParts();
            $this->savePreview();

            $this->tmp->delete();
        }

        return true;
    }

    /**
     * @return self
     */
    private function createPreviewParts(): self
    {
        $ranges = $this->getRanges();

        $parts = [1, 3, 5, 7];

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

            $this->collection[] = $path;
        }

        return $this;
    }

    private function savePreview(): void
    {
        $video = $this->getVideo();
        $video->concat($this->collection)
              ->saveFromSameCodecs($this->tmp->path('preview.mp4'), true);

        copy(
            $this->tmp->path('preview.mp4'),
            $this->getRootPath().'/conversions/'.$this->getPreviewName()
        );

        $this->media->markAsConversionGenerated('preview', true);
    }

    /**
     * @return string
     */
    private function getRootPath(): string
    {
        return dirname($this->media->getPath());
    }

    /**
     * @return string
     */
    private function getPreviewName(): string
    {
        return pathinfo($this->media->file_name, PATHINFO_FILENAME).'-preview.mp4';
    }

    /**
     * @param int $divider
     *
     * @return array
     */
    private function getRanges(int $divider = 8): array
    {
        $duration = $this->media->getCustomProperty('duration');
        $steps = $duration / $divider;

        return range(0, $duration, $steps);
    }

    /**
     * @return Video
     */
    private function getVideo(): Video
    {
        return $this->ffmpeg->open($this->media->getPath());
    }

    /**
     * @return bool
     */
    protected function hasValidMime(string $type): bool
    {
        return false !== strpos($this->media->mime_type, $type);
    }
}
