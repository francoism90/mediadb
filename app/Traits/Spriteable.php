<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

trait Spriteable
{
    public function getSpriteContents(array $frames = [], User $user): string
    {
        $cacheKey = "sprite_{$this->getRouteKey()}_{$user->id}";

        $contents = Cache::remember($cacheKey, 60 * 10, function () use ($frames, $user) {
            $str = "WEBVTT\n\n";

            foreach ($frames as $frame) {
                $str .= "{$frame['start']} --> {$frame['end']}\n";

                $frame['url'] = $this->getSpriteUrl($frame['sprite'], $user);

                $str .= json_encode($frame, JSON_UNESCAPED_SLASHES)."\n\n";
            }

            return $str;
        });

        return $contents;
    }

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
