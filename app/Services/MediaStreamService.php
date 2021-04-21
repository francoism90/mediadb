<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use App\Services\Streamers\DashStreamer;
use App\Services\Tokenizers\LocalTokenizer;
use Illuminate\Support\Collection;

class MediaStreamService
{
    protected $streamer;

    protected $tokenizer;

    public function __construct()
    {
        $this->streamer = resolve($this->getStreamModule());
        $this->tokenizer = resolve($this->getTokenModule());
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return string
     */
    public function getMappingUrl(Media $media, User $user): string
    {
        $token = $this->tokenizer->create(
            ['media' => $media, 'user' => $user],
            config('media.vod_expires', 60 * 24)
        );

        $this->streamer->setToken($token);

        return $this->streamer->getUrl();
    }

    /**
     * @param Media $media
     *
     * @return Collection
     */
    public function getResponseFormat(Media $media): Collection
    {
        return collect([
            'id' => $media->id,
            'sequences' => (array) [
                [
                    'id' => $media->id,
                    'label' => $media->name,
                    'clips' => (array) [
                        [
                            'type' => 'source',
                            'path' => $media->getPath(),
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    public function validToken(string $token): bool
    {
        return $this->tokenizer->exists($token);
    }

    /**
     * @param string $token
     *
     * @return array
     */
    public function decodeToken(string $token): array
    {
        return $this->tokenizer->find($token);
    }

    /**
     * @return string
     */
    protected function getStreamModule(): string
    {
        return config('media.stream_module', DashStreamer::class);
    }

    /**
     * @return string
     */
    protected function getTokenModule(): string
    {
        return config('media.token_module', LocalTokenizer::class);
    }
}
