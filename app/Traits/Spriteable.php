<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

trait Spriteable
{
    /**
     * @param array $frames
     * @param User  $user
     *
     * @return string
     */
    public function getSpriteContents(array $frames = [], User $user): string
    {
        // Return on empty frames
        if (!$frames) {
            return '';
        }

        $cacheKey = "sprite_{$this->getRouteKey()}_{$user->id}";

        $contents = Cache::remember($cacheKey, 60 * 60 * 24, function () use ($frames, $user) {
            $str = "WEBVTT\n\n";

            foreach ($frames as $frame) {
                // Sprite url must be unique for the user
                $frame['url'] = $this->getSpriteUrl($frame['sprite'], $user);

                $str .= "{$frame['start']} --> {$frame['end']}\n";
                $str .= json_encode($frame, JSON_UNESCAPED_SLASHES)."\n\n";
            }

            return $str;
        });

        return $contents;
    }

    /**
     * @param int  $id
     * @param User $user
     *
     * @return string
     */
    public function getSpriteUrl(int $id, User $user): string
    {
        return URL::signedRoute(
            'api.media.asset',
            [
                'media' => $this,
                'user' => $user,
                'name' => "sprite-{$id}.webp",
            ]
        );
    }
}
