<?php

namespace App\Services;

use App\Models\Video;
use App\Services\Streamers\DashStreamer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VodService
{
    public DashStreamer $streamer;

    public function __construct(
        protected Model $model
    )
    {
        $this->streamer = app($this->getStreamModule(), ['model' => $this->model]);
    }

    public function getManifestUrl(): string
    {
        return $this->streamer->getUrl('dash', 'manifest.mpd');
    }

    public function getManifestContents(): Collection
    {
        return $this->streamer->getManifestContents();
    }

    protected function getStreamModule(): string
    {
        return config('api.stream_module', DashStreamer::class);
    }
}
