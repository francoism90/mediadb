<?php

namespace App\Services\Media;

use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StreamService
{
    public const MAPPING_PATH = 'dash';
    public const MAPPING_MANIFEST = 'manifest.mpd';

    /**
     * @param Media       $media
     * @param User        $user
     * @param string|null $type
     *
     * @return string
     */
    public function getUrl(
        Media $media,
        User $user,
        ?string $type = null
    ): string {
        // Get encrypted url
        $url = $this->getEncryptedUrl($media, $type);

        // Generate nginx expire url
        $id = "{$user->getRouteKey()}_{$media->getRouteKey()}";
        $expires = time() + config('vod.expire', 60 * 60 * 8);
        $secret = config('vod.secret');

        $md5 = md5("{$expires}{$id} {$secret}", true);
        $md5 = base64_encode($md5);
        $md5 = strtr($md5, '+/', '-_');
        $md5 = str_replace('=', '', $md5);

        return "{$url}?md5={$md5}&id={$id}&expires={$expires}";
    }

    /**
     * @param Media       $media
     * @param string|null $type
     *
     * @return string
     */
    protected function getEncryptedUrl(Media $media, ?string $type = null): string
    {
        // Generate streamKey
        $streamKey = Str::uuid();

        // Generate media mapping file
        $jsonContents = $this->getMediaMapping($media, $type);

        // Write mapping file
        $this->writeMappingFile($streamKey, $jsonContents);

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
    protected function getMediaMapping(Media $media, ?string $type = null): string
    {
        $clips = $this->getClipSourceMapping($media, $type);

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
    protected function getClipSourceMapping(Media $media, ?string $type = null): array
    {
        switch ($type) {
            // case: 'preview':
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
        // e.g. <uuid>.json/manifest.mpd
        $path = "{$streamKey}.json".'/'.self::MAPPING_MANIFEST;

        $hash = substr(
            md5($path, true),
            0,
            $this->getStreamHashSize()
        );

        return $hash.$path;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function generateEncryptedMappingPath(string $path): string
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
     * @param string $streamKey
     * @param string $contents
     *
     * @return bool
     */
    protected function writeMappingFile(
        string $streamKey,
        string $contents
    ): bool {
        return Storage::disk('streams')->put("{$streamKey}.json", $contents);
    }
}
