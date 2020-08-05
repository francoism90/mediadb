<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class MediaSpriteTrackService
{
    public const SPRITETRACK_EXPIRE = 60 * 5;

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return string
     */
    public function getTrack(Media $media, User $user): string
    {
        $cacheKey = "sprite_{$media->id}_{$user->id}";

        return Cache::remember(
            $cacheKey,
            self::SPRITETRACK_EXPIRE,
            fn () => $this->getSpriteTrackContents($media, $user)
        );
    }

    /**
     * @param Media $media
     * @param User  $user
     *
     * @return string
     */
    protected function getSpriteTrackContents(Media $media, User $user): string
    {
        $frames = $this->getSpriteFrames($media);

        $vtt = "WEBVTT\n\n";

        foreach ($frames as $frame) {
            $frame['url'] = $this->getSpriteAssetUrl(
                $media,
                $user,
                $frame['sprite']
            );

            $vtt .= "{$frame['start']} --> {$frame['end']}\n";
            $vtt .= json_encode($frame, JSON_UNESCAPED_SLASHES)."\n\n";
        }

        return $vtt;
    }

    /**
     * @param Media $media
     *
     * @return array
     */
    protected function getSpriteFrames(Media $media): array
    {
        $path = $media->getBaseMediaPath().'conversions/sprite.json';

        if (!file_exists($path)) {
            return [];
        }

        $contents = file_get_contents($path);

        $frames = json_decode($contents, true);

        return $frames;
    }

    /**
     * @param Media $media
     * @param User  $user
     * @param int   $id
     *
     * @return string
     */
    protected function getSpriteAssetUrl(
        Media $media,
        User $user,
        int $id
    ): string {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $media,
                'user' => $user,
                'name' => "sprite-{$id}.webp",
            ]
        );
    }
}
