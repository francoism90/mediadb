<?php

namespace App\Services;

use App\Models\User;
use App\Models\Video;
use App\Services\Streamers\DashStreamer;
use App\Services\Tokenizers\LocalTokenizer;
use Illuminate\Support\Collection;

class VideoStreamService
{
    protected $streamer;

    protected $tokenizer;

    public function __construct()
    {
        $this->streamer = resolve($this->getStreamModule());
        $this->tokenizer = resolve($this->getTokenModule());
    }

    /**
     * @param Video $video
     * @param User  $user
     *
     * @return string
     */
    public function getMappingUrl(Video $video, User $user): string
    {
        $token = $this->tokenizer->create(
            ['video' => $video, 'user' => $user],
            config('video.vod_expires', 60 * 24)
        );

        $this->streamer->setToken($token);

        return $this->streamer->getUrl();
    }

    /**
     * @param Video $video
     *
     * @return Collection
     */
    public function getResponseFormat(Video $video): Collection
    {
        $clip = $video->getFirstMedia('clip');

        $collect = collect([
            'id' => $video->id,
            'sequences' => (array) [
                [
                    'id' => $clip->id,
                    'label' => $clip->name,
                    'clips' => (array) [
                        [
                            'type' => 'source',
                            'path' => $clip->getPath(),
                        ],
                    ],
                ],
            ],
        ]);

        return $collect;
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
        return config('video.vod_stream_module', DashStreamer::class);
    }

    /**
     * @return string
     */
    protected function getTokenModule(): string
    {
        return config('video.vod_token_module', LocalTokenizer::class);
    }
}
