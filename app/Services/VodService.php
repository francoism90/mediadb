<?php

namespace App\Services;

use App\Services\Streamers\DashStreamer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class VodService
{
    public DashStreamer $streamer;

    public function __construct(
        protected Model $model
    ) {
        $this->streamer = app($this->getStreamModule(), ['model' => $this->model]);
    }

    public function getManifestUrl(): string
    {
        return $this->streamer->getManifestUrl();
    }

    public function getManifestContents(): Collection
    {
        return $this->streamer->getManifestContents();
    }

    public function getSpriteContents(): string
    {
        return $this->streamer->getSpriteContents();
    }

    protected function getStreamModule(): string
    {
        return config('api.stream_module', DashStreamer::class);
    }
}
