<?php

namespace App\Services;

use App\Events\Media\HasBeenAdded;
use App\Models\Media;
use FFMpeg\FFMpeg;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Throwable;

class MediaService
{
    /**
     * @var FFMpeg
     */
    protected $ffmpeg;

    public function __construct()
    {
        $this->ffmpeg = FFMpeg::create([
            'ffmpeg.binaries' => config('media-library.ffmpeg_path'),
            'ffmpeg.threads' => config('media-library.ffmpeg_threads', 0),
            'ffmpeg.timeout' => 180,
            'ffprobe.binaries' => config('media-library.ffprobe_path'),
            'ffprobe.timeout' => config('media-library.ffprobe_timeout', 60),
            'timeout' => 180,
        ]);
    }

    /**
     * @param Model       $model
     * @param SplFileInfo $file
     * @param string      $collection
     * @param array       $properties
     *
     * @return void
     */
    public function import(
        Model $model,
        SplFileInfo $file,
        string $collection = null,
        array $properties = []
    ): void {
        try {
            $filePath = $file->getRealPath();
            $fileExtension = $file->getExtension();

            $media = $model
                    ->addMedia($filePath)
                    ->withCustomProperties($properties)
                    ->toMediaCollection($collection);

            // Force WebVTT
            if ('vtt' === $fileExtension) {
                $media->mime_type = 'text/vtt';
                $media->save();
            }

            event(new HasBeenAdded($model, $media));
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * @param string $path
     *
     * @return Finder
     */
    public function getFiles(string $path): Finder
    {
        $filter = function (SplFileInfo $file) {
            return $file->isReadable() && $file->isWritable();
        };

        return (new Finder())
            ->files()
            ->in($path)
            ->depth('== 0')
            ->filter($filter)
            ->sortByName();
    }

    /**
     * @param Media $media
     *
     * @return void
     */
    public function setAttributes(Media $media): void
    {
        $collection = $this->getFormatAttributes($media->getPath());

        $attributes = $collection->merge(
            $this->getVideoAttributes($media->getPath())
        );

        $media
            ->setCustomProperty('metadata', $attributes->all())
            ->save();
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function isProbable(string $path): bool
    {
        return $this->ffmpeg->getFFProbe()->isValid($path);
    }

    /**
     * @param string $path
     *
     * @return Collection
     */
    public function getFormatAttributes(string $path): Collection
    {
        $format = $this->ffmpeg->getFFProbe()->format($path);

        if (!$format) {
            return collect();
        }

        return collect([
            'start_time' => $format->get('start_time', 0),
            'duration' => $format->get('duration', 0),
            'size' => $format->get('size', 0),
            'bitrate' => $format->get('bit_rate', 0),
            'probe_score' => $format->get('probe_score', 0),
            'tags' => $format->get('tags', []),
        ]);
    }

    /**
     * @param string $path
     *
     * @return Collection
     */
    public function getVideoAttributes(string $path): Collection
    {
        $stream = $this->ffmpeg->getFFProbe()->streams($path)->videos()->first();

        if (!$stream) {
            return collect();
        }

        return collect([
            'codec_name' => $stream->get('codec_name', null),
            'profile' => $stream->get('profile', null),
            'width' => $stream->get('width', 0),
            'height' => $stream->get('height', 0),
            'coded_width' => $stream->get('coded_width', 0),
            'coded_height' => $stream->get('coded_height', 0),
            'closed_captions' => $stream->get('closed_captions', 0),
            'pix_fmt' => $stream->get('pix_fmt', 0),
            'display_aspect_ratio' => $stream->get('display_aspect_ratio', null),
        ]);
    }
}
