<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

trait Streamable
{
    /**
     * @return string
     */
    public function getStreamUrl(string $path = 'dash', string $uri = 'manifest.mpd'): string
    {
        // Write json contents for upstream
        $this->writeStreamJson();

        // Create the signed url
        $signedUrl = $this->getStreamSignedUrl($uri);

        // add PKCS#7 padding
        $pad = 16 - (strlen($signedUrl) % 16);
        $signedUrl .= str_repeat(chr($pad), $pad);

        // Encrypt url
        $encrypted = $this->getStreamUrlEncrypted($signedUrl);

        // Base64 encode
        $encoded = rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');

        return config('vod.url')."/{$path}/{$encoded}";
    }

    /**
     * @return bool
     */
    protected function writeStreamJson(int $expires = 300): bool
    {
        $key = str_replace('.json', '', $this->getStreamJsonName());

        if (!Storage::disk('streams')->exists($this->getStreamJsonName())) {
            Cache::forget($key);
        }

        return Cache::remember($key, $expires, fn () => Storage::disk('streams')->put(
            $this->getStreamJsonName(), $this->getStreamJsonContents()
        ));
    }

    /**
     * @return string
     */
    protected function getStreamJsonName(): string
    {
        $userId = auth()->user()->id ?? 0;

        return "{$this->table}_{$this->id}_{$userId}.json";
    }

    /**
     * @return string
     */
    protected function getStreamJsonContents(): string
    {
        $contents = [
            'id' => $this->getRouteKey(),
            'sequences' => (array) [
                [
                    'id' => $this->getRouteKey(),
                    'label' => $this->name ?? $this->getRouteKey(),
                    'clips' => [
                        [
                            'type' => 'source',
                            'path' => $this->getPath(),
                        ],
                    ],
                ],
            ],
        ];

        return json_encode(
            $contents,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
    }

    /**
     * @return string
     */
    protected function getStreamSignedUrl(string $uri): string
    {
        if (!function_exists('openssl_encrypt')) {
            throw new Exception('openssl_encrypt is required');
        }

        // e.g. example.json/manifest.mpd
        $path = $this->getStreamJsonName().'/'.$uri;

        $hash = substr(
            md5($path, true), 0, $this->getStreamHashSize()
        );

        return $hash.$path;
    }

    /**
     * @param string $signedUrl
     *
     * @return string
     */
    protected function getStreamUrlEncrypted(string $signedUrl): string
    {
        return openssl_encrypt(
            $signedUrl,
            'aes-256-cbc',
            $this->getStreamKey(),
            OPENSSL_RAW_DATA | OPENSSL_NO_PADDING,
            $this->getStreamIV()
        );
    }

    /**
     * @return string
     */
    protected function getStreamKey(): string
    {
        return pack('H*', config('vod.key'));
    }

    /**
     * @return string
     */
    protected function getStreamIV(): string
    {
        return pack('H*', config('vod.iv'));
    }

    /**
     * @return int
     */
    protected function getStreamHashSize(): int
    {
        return config('vod.secret.hash_size', 8);
    }
}
