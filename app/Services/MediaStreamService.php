<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class MediaStreamService
{
    public const MAPPING_PATH = 'dash';
    public const MAPPING_MANIFEST = 'manifest.mpd';
    public const MAPPING_CACHE = 900;

    /**
     * @param Media  $media
     * @param string $streamKey
     * @param string $requestIp
     *
     * @return string
     */
    public function getExpireUrl(
        Media $media,
        string $streamKey,
        string $requestIp,
        ?string $type = null
    ): string {
        // Get encrypted url
        $url = $this->getUrl($media, $streamKey, $type);

        // Generate nginx expire url
        $id = $media->getRouteKey() ?? $media->id;
        $expires = time() + config('vod.expire', 60 * 60 * 3);
        $secret = config('vod.secret');

        $md5 = md5("{$expires}{$id}{$requestIp} {$secret}", true);
        $md5 = base64_encode($md5);
        $md5 = strtr($md5, '+/', '-_');
        $md5 = str_replace('=', '', $md5);

        return "{$url}?md5={$md5}&id={$id}&expires={$expires}";
    }

    /**
     * @param Media  $media
     * @param string $streamKey
     *
     * @return string
     */
    protected function getUrl(Media $media, string $streamKey, ?string $type = null): string
    {
        // Generate media mapping file
        $jsonContents = $this->getMappingContents($media, $type);

        // Write mapping file
        $this->writeMappingCacheFile($streamKey, $jsonContents);

        // Get hash path
        $hashPath = $this->generateMappingHashPath($streamKey);

        // Add PKCS#7 padding
        $pad = 16 - (strlen($hashPath) % 16);
        $hashPath .= str_repeat(chr($pad), $pad);

        // Encrypt path
        $encryptedPath = $this->generateEncryptedMappingPath($hashPath);

        // Base64 path
        $encodedPath = rtrim(strtr(base64_encode($encryptedPath), '+/', '-_'), '=');

        return config('vod.url').'/'.self::MAPPING_PATH."/{$encodedPath}";
    }

    /**
     * @param Media       $media
     * @param string|null $type
     *
     * @return string
     */
    protected function getMappingContents(Media $media, ?string $type = null): string
    {
        $clips = $this->getClipsContents($media, $type);

        $contents = [
            'id' => $media->id,
            'sequences' => (array) [
                [
                    'id' => $media->id,
                    'label' => $media->name,
                    'clips' => $clips,
                ],
            ],
        ];

        return json_encode(
            $contents,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
        );
    }

    /**
     * @param Media       $media
     * @param string|null $type
     *
     * @return array
     */
    protected function getClipsContents(Media $media, ?string $type = null): array
    {
        switch ($type) {
            case 'preview':
                $clips = [
                    [
                        'type' => 'source',
                        'path' => $media->getBaseMediaPath().'conversions/preview.mp4',
                    ],
                ];
            break;

            default:
                $clips = [
                    [
                        'type' => 'source',
                        'path' => $media->getPath(),
                    ],
                ];
        }

        return $clips;
    }

    /**
     * @param string $streamKey
     *
     * @return string
     */
    protected function generateMappingHashPath(string $streamKey): string
    {
        // e.g. media_1_50.json/manifest.mpd
        $path = "{$streamKey}.json".'/'.self::MAPPING_MANIFEST;

        $hash = substr(
            md5($path, true), 0, $this->getStreamHashSize()
        );

        return $hash.$path;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    protected function generateEncryptedMappingPath(string $url): string
    {
        return openssl_encrypt(
            $url,
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

    /**
     * @param string    $streamKey
     * @param string    $contents
     * @param bool|null $force
     *
     * @return bool
     */
    protected function writeMappingCacheFile(
        string $streamKey,
        string $contents,
        ?bool $force = false
    ): bool {
        if (!Storage::disk('streams')->exists("{$streamKey}.json") || $force) {
            Cache::forget($streamKey);
        }

        return Cache::remember(
            $streamKey,
            self::MAPPING_CACHE,
            fn () => Storage::disk('streams')->put("{$streamKey}.json", $contents)
        );
    }
}
