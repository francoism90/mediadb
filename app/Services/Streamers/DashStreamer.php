<?php

namespace App\Services\Streamers;

use App\Models\Media;
use App\Services\SpriteService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DashStreamer implements StreamerInterface
{
    protected string $token = '';

    public function getUrl(string $location, string $uri): string
    {
        $url = config('api.vod_url');

        $hash = $this->getManifestHash($uri);

        $hashPath = $this->getEncryptedPath($hash);
        $hashPath = $this->getEncodedPath($hashPath);

        return sprintf('%s/%s/%s', $url, $location, $hashPath);
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getMapping(Model $model): Collection
    {
        $sequences = collect([
            $this->getSequence($model, 'clip')->toArray(),
            $this->getSequence($model, 'caption')->toArray(),
            $this->getSpriteSequence($model)->toArray(),
        ]);

        $foo = collect([
            'id' => $model->getRouteKey(),
            'sequences' => $sequences->filter()->values(),
        ]);

        logger($foo->toArray());

        return $foo;
    }

    protected function getSequence(Model $model, string $collection): Collection
    {
        return $model->getMedia($collection)->flatMap(function (Media $media) {
            return [
                'language' => $media->getCustomProperty('locale', 'eng'),
                'clips' => [
                    [
                        'type' => 'source',
                        'path' => $media->getPath(),
                    ],
                ],
            ];
        });
    }

    protected function getSpriteSequence(Model $model): Collection
    {
        $path = app(SpriteService::class)->create($model);

        return collect([
            'label' => 'sprite',
            'clips' => [
                [
                    'type' => 'source',
                    'path' => $path,
                ],
            ],
        ]);
    }

    protected function getManifestHash(string $uri): string
    {
        $path = $this->getManifestRoute().sprintf('/%s', $uri);

        $hash = substr(
            md5($path, true),
            0,
            $this->getStreamHashSize()
        );

        // Add hash prefix
        $path = $hash.$path;

        // Add PKCS#7 padding
        $pad = 16 - (strlen($path) % 16);

        return $path.str_repeat(chr($pad), $pad);
    }

    protected function getManifestRoute(): string
    {
        $route = route('api.vod.manifest', ['token' => $this->token], false);

        return trim($route, '/');
    }

    protected function getEncryptedPath(string $path): string
    {
        return openssl_encrypt(
            $path,
            'aes-256-cbc',
            $this->getStreamKey(),
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            $this->getStreamIV()
        );
    }

    protected function getEncodedPath(string $path): string
    {
        return rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
    }

    protected function getStreamKey(): string
    {
        return pack('H*', config('api.vod_key'));
    }

    protected function getStreamIV(): string
    {
        return pack('H*', config('api.vod_iv'));
    }

    protected function getStreamHashSize(): int
    {
        return config('api.vod_hash_size', 8);
    }
}
