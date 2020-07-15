<?php

namespace App\Jobs\Media;

use App\Models\Media;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\Stream;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SetAttributes implements ShouldQueue
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
    public $timeout = 300;

    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

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
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
        ]);

        if (!$this->isProbable()) {
            throw new \Exception('Unable to probe file.');
        }

        $this->setProperties();
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    protected function setProperties(): bool
    {
        $format = $this->getFormat();
        $stream = $this->getFirstStream();

        $this->media->setCustomProperty('duration', $format->get('duration', 0));
        $this->media->setCustomProperty('bitrate', $format->get('bit_rate', 0));
        $this->media->setCustomProperty('codec_name', $stream->get('codec_name', null));
        $this->media->setCustomProperty('profile', $stream->get('profile', null));
        $this->media->setCustomProperty('aspect_ratio', $stream->get('display_aspect_ratio', null));
        $this->media->setCustomProperty('width', $stream->get('width', 0));
        $this->media->setCustomProperty('height', $stream->get('height', 0));

        return $this->media->save();
    }

    /**
     * @return Format
     */
    protected function getFormat(): Format
    {
        return $this->ffmpeg
            ->getFFProbe()
            ->format(
                $this->media->getPath()
            );
    }

    /**
     * @return Stream
     */
    protected function getFirstStream(): Stream
    {
        return $this->ffmpeg
            ->getFFProbe()
            ->streams(
                $this->media->getPath()
            )
            ->first();
    }

    /**
     * @return bool
     */
    protected function isProbable(): bool
    {
        return $this->ffmpeg
            ->getFFProbe()
            ->isValid(
                $this->media->getPath()
            );
    }
}
