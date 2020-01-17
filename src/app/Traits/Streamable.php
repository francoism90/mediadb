<?php

namespace App\Traits;

use Exception;
use Illuminate\Support\Facades\Storage;

trait Streamable
{
    /**
     * @return string
     */
    public function getStreamJsonUrl(): string
    {
        // Write json for upstream vod
        Storage::disk('streams')
            ->put($this->getStreamJsonName(), $this->getStreamJsonContents());

        return $this->getStreamManifestUrl();
    }

    /**
     * @return string
     */
    private function getStreamJsonContents(): string
    {
        $contents = [
            'sequences' => (array) [
                [
                    'clips' => [
                        [
                            'id' => $this->getRouteKey(),
                            'label' => $this->name ?? $this->getRouteKey(),
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
    private function getStreamManifestUrl(): string
    {
        $signedUrl = $this->getStreamSignedUrl();

        // add PKCS#7 padding
        $pad = 16 - (strlen($signedUrl) % 16);
        $signedUrl .= str_repeat(chr($pad), $pad);

        // Encrypt url
        $encrypted = $this->getStreamUrlEncrypted($signedUrl);

        // Base64 encode
        $encoded = rtrim(strtr(base64_encode($encrypted), '+/', '-_'), '=');

        return $this->getStreamBaseUrl().$encoded;
    }

    /**
     * @return string
     */
    private function getStreamSignedUrl(): string
    {
        if (!function_exists('openssl_encrypt')) {
            throw new Exception('openssl_encrypt is required');
        }

        $hash = substr(
            md5($this->getStreamManifestPath(), true), 0, $this->getStreamHashSize()
        );

        return $hash.$this->getStreamManifestPath();
    }

    /**
     * @param string $signedUrl
     *
     * @return string
     */
    private function getStreamUrlEncrypted(string $signedUrl): string
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
    private function getStreamBaseUrl(): string
    {
        return config('vod.url').'/';
    }

    /**
     * @return string
     */
    private function getStreamManifestPath(): string
    {
        return $this->getStreamJsonName().'/manifest.mpd';
    }

    /**
     * @return string
     */
    private function getStreamJsonName(): string
    {
        $userId = auth()->user()->id ?? 0;

        return "{$this->table}_{$this->id}_{$userId}.json";
    }

    /**
     * @return string
     */
    private function getStreamKey(): string
    {
        return pack('H*', config('vod.key'));
    }

    /**
     * @return string
     */
    private function getStreamIV(): string
    {
        return pack('H*', config('vod.iv'));
    }

    /**
     * @return int
     */
    private function getStreamHashSize(): int
    {
        return config('vod.secret.hash_size', 8);
    }
}
