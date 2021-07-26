<?php

namespace App\Services;

use App\Models\Video;
use App\Services\Streamers\DashStreamer;
use App\Services\Tokenizers\LocalTokenizer;
use Illuminate\Support\Collection;

class VodService
{
    protected DashStreamer $streamer;
    protected LocalTokenizer $tokenizer;

    public function __construct()
    {
        $this->streamer = app($this->getStreamModule());
        $this->tokenizer = app($this->getTokenModule());
    }

    public function generateUrl(string $location, string $uri, array $token = []): string
    {
        $tokenKey = $this->tokenizer->create($token);

        $this->streamer->setToken($tokenKey);

        return $this->streamer->getUrl($location, $uri);
    }

    public function getFormat(Video $video, string $collection = 'clip'): Collection
    {
        return collect([
            'id' => $video->getRouteKey(),
            'sequences' => [
                [
                    'label' => $video->name,
                    'clips' => [
                        [
                            'type' => 'source',
                            'path' => $video->getFirstMediaPath($collection),
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function validToken(string $token): bool
    {
        return $this->tokenizer->exists($token);
    }

    public function decodeToken(string $token): array
    {
        return $this->tokenizer->find($token);
    }

    protected function getStreamModule(): string
    {
        return config('api.stream_module', DashStreamer::class);
    }

    protected function getTokenModule(): string
    {
        return config('api.token_module', LocalTokenizer::class);
    }
}
