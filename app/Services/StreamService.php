<?php

namespace App\Services;

use App\Models\Media;
use App\Services\Streamers\DashStreamer;
use App\Services\Tokenizers\LocalTokenizer;
use Illuminate\Support\Collection;

class StreamService
{
    protected $streamer;

    protected $tokenizer;

    public function __construct()
    {
        $this->streamer = resolve($this->getStreamModule());
        $this->tokenizer = resolve($this->getTokenModule());
    }

    public function getMappingUrl(string $location, string $uri, array $token = []): string
    {
        $token = $this->tokenizer->create(
            $token,
            config('media.vod_expires', 60 * 24)
        );

        $this->streamer->setToken($token);

        return $this->streamer->getUrl($location, $uri);
    }

    public function getResponseFormat(Media $media): Collection
    {
        return collect([
            'id' => $media->id,
            'sequences' => [
                [
                    'id' => $media->id,
                    'label' => $media->name,
                    'clips' => [
                        [
                            'type' => 'source',
                            'path' => $media->getPath(),
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

    public function getStreamModule(): string
    {
        return config('media.stream_module', DashStreamer::class);
    }

    public function getTokenModule(): string
    {
        return config('media.token_module', LocalTokenizer::class);
    }
}
