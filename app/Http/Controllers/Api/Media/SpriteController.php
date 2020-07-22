<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;

class SpriteController extends Controller
{
    /**
     * @param Media $media
     * @param User  $user
     *
     * @return mixed
     */
    public function __invoke(Media $media, User $user)
    {
        if (!$media->hasGeneratedConversion('sprite')) {
            abort(404);
        }

        $path = $media->getBaseMediaPath().'conversions/sprite.json';
        $json = file_get_contents($path);

        // Decode json
        $frames = json_decode($json, true);

        // Get frames
        $contents = $media->getSpriteContents($frames, $user);

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'text/vtt',
            ]);
    }
}
