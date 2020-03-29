<?php

namespace App\Listeners\Media;

use Exception;
use FFMpeg\FFMpeg;
use FFMpeg\FFProbe\DataMapping\Format;
use FFMpeg\FFProbe\DataMapping\Stream;
use Spatie\MediaLibrary\MediaCollections\Events\MediaHasBeenAdded;

class SetAttributes
{
    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

    /**
     * @var Media
     */
    protected $media;

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
     * @param object $event
     */
    public function handle(MediaHasBeenAdded $event)
    {
        $this->media = $event->media;

        if (!$this->hasValidMime('video')) {
            throw new Exception('Invalid video mimetype.');
        }

        $this->setVideoProperties();
    }

    /**
     * @return bool
     *
     * @throws \Exception
     */
    private function setVideoProperties(): bool
    {
        if (!$this->isProbable()) {
            throw new Exception('Unable to probe media file.');
        }

        $format = $this->getFormat();
        $stream = $this->getStream();

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
    private function getFormat(): Format
    {
        return $this->ffmpeg->getFFProbe()->format($this->media->getPath());
    }

    /**
     * @return Stream
     */
    private function getStream(): Stream
    {
        return $this->ffmpeg->getFFProbe()->streams($this->media->getPath())->first();
    }

    /**
     * @return bool
     */
    private function isProbable(): bool
    {
        return $this->ffmpeg->getFFProbe()->isValid($this->media->getPath());
    }

    /**
     * @return bool
     */
    protected function hasValidMime(string $type): bool
    {
        return false !== strpos($this->media->mime_type, $type);
    }
}
