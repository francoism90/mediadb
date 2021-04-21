<?php

namespace App\Services\Streamers;

class DashStreamer implements StreamerInterface
{
    public const DASH_PATH = 'dash';
    public const DASH_MANIFEST = 'manifest.mpd';

    /**
     * @var string
     */
    protected string $token;

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $hash = $this->getManifestHash();

        $path = $this->getEncryptedPath($hash);
        $path = $this->getEncodedPath($path);

        return config('media.vod_url').'/'.self::DASH_PATH."/{$path}";
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return string
     */
    protected function getManifestHash(): string
    {
        $path = $this->getManifestPath().'/'.self::DASH_MANIFEST;

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

    /**
     * @return string
     */
    protected function getManifestPath(): string
    {
        $route = route('api.media.manifest', ['token' => $this->token], false);

        return trim($route, '/');
    }

    /**
     * @param string $path
     *
     * @return string
     */
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

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getEncodedPath(string $path): string
    {
        return rtrim(strtr(base64_encode($path), '+/', '-_'), '=');
    }

    /**
     * @return string
     */
    protected function getStreamKey(): string
    {
        return pack('H*', config('media.vod_key'));
    }

    /**
     * @return string
     */
    protected function getStreamIV(): string
    {
        return pack('H*', config('media.vod_iv'));
    }

    /**
     * @return int
     */
    protected function getStreamHashSize(): int
    {
        return config('media.vod_hash_size', 8);
    }
}
