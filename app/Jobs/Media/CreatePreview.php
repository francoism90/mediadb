<?php

namespace App\Jobs\Media;

use App\Models\Media;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\Video\ResizeFilter;
use FFMpeg\Format\Video\DefaultVideo;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264;
use FFMpeg\Media\Video;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Spatie\MediaLibrary\MediaCollections\Filesystem;
use Spatie\MediaLibrary\Support\TemporaryDirectory;

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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);

        $video = $ffmpeg->open($this->media->getPath());
        $duration = $ffmpeg->getFFProbe()->format($this->media->getPath())->get('duration');

        // Prepare the conversion
        $tempDirectory = TemporaryDirectory::create();

        $path = $tempDirectory->path($this->getConversionFileName());

        $clips = $this->prepareConversion($tempDirectory, $video, $duration);

        $concat = $video->concat($clips);
        $concat->saveFromSameCodecs($path);

        /** @var \Spatie\MediaLibrary\MediaCollections\Filesystem $filesystem */
        $filesystem = app(Filesystem::class);

        $filesystem->copyToMediaLibrary(
            $path,
            $this->media,
            'conversions',
            $this->getConversionFileName()
        );

        $this->media->markAsConversionGenerated('preview', true);

        $tempDirectory->delete();
    }

    /**
     * @return self
     */
    protected function prepareConversion($tempDirectory, Video $video, float $duration = 0): array
    {
        $steps = $duration / 8;
        $range = range(0, $duration, $steps);
        $parts = [1, 3, 5, 7];

        $clips = [];

        foreach ($parts as $part) {
            $path = $tempDirectory->path("{$part}.{$this->getMediaExtension()}");

            $clip = $video->clip(
                TimeCode::fromSeconds($range[$part]), TimeCode::fromSeconds(2)
            );

            $clip->addFilter(
                    new SimpleFilter(['-an']) // disable audio codec
                )
                 ->filters()
                 ->resize(
                     new Dimension(320, 240), ResizeFilter::RESIZEMODE_INSET, true
                );

            $clip->save(
                $this->getVideoFormat(),
                $path
            );

            $clips[] = $path;
        }

        return $clips;
    }

    /**
     * @return DefaultVideo
     */
    protected function getVideoFormat(): DefaultVideo
    {
        switch ($this->media->mime_type) {
            case 'video/ogg':
            case 'video/vp8':
            case 'video/vp9':
            case 'video/webm':
            case 'video/x-ogg':
            case 'video/x-ogm':
            case 'video/x-ogm+ogg':
            case 'video/x-theora':
            case 'video/x-theora+ogg':
                $format = new WebM();
                $format->setKiloBitrate(200);
                break;
            default:
                $format = new X264();
                $format->setKiloBitrate(200);
        }

        return $format;
    }

    /**
     * @return string
     */
    protected function getMediaExtension(): string
    {
        return pathinfo($this->media->file_name, PATHINFO_EXTENSION);
    }

    /**
     * @return string
     */
    protected function getConversionFileName(): string
    {
        $filename = pathinfo($this->media->file_name, PATHINFO_FILENAME);

        return "{$filename}-preview.{$this->getMediaExtension()}";
    }
}
