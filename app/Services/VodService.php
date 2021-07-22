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
        $this->streamer = resolve($this->getStreamModule());
        $this->tokenizer = resolve($this->getTokenModule());
    }

    public function getTemporaryUrl(string $location, string $uri, array $token = []): string
    {
        $tokenKey = $this->tokenizer->create($token);

        $this->streamer->setToken($tokenKey);

        return $this->streamer->getUrl($location, $uri);
    }

    public function getSequencesFormat(Video $video, string $collection = 'clip'): Collection
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
        return config('media.stream_module', DashStreamer::class);
    }

    protected function getTokenModule(): string
    {
        return config('media.token_module', LocalTokenizer::class);
    }
}
