<?php

namespace App\Services;

use App\Models\Video;

class VideoDashService
{
    public function generateUrl(Video $video, string $location, string $uri): string
    {
        $hash = $this->getManifestHash($video, $uri);

        $hashPath = $this->getEncryptedPath($hash);
        $hashPath = $this->getEncodedPath($hashPath);

        return sprintf('%s/%s/%s', config('api.vod.url'), $location, $hashPath);
    }

    protected function getManifestHash(Video $video, string $uri): string
    {
        $path = sprintf('%s/%s', $this->getManifestRoute($video), $uri);

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

    protected function getManifestRoute(Video $video): string
    {
        $route = route('api.videos.manifest', ['video' => $video], false);

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
        return pack('H*', config('api.vod.key'));
    }

    protected function getStreamIV(): string
    {
        return pack('H*', config('api.vod.iv'));
    }

    protected function getStreamHashSize(): int
    {
        return config('api.vod.hash_size', 8);
    }
}
