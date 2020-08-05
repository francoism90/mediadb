<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaSpriteTrackService;

class SpriteController extends Controller
{
    /**
     * @var MediaSpriteTrackService
     */
    protected $mediaSpriteService;

    public function __construct(MediaSpriteTrackService $mediaSpriteService)
    {
        $this->mediaSpriteService = $mediaSpriteService;
    }

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

        $contents = $this->mediaSpriteService->getTrack(
            $media,
            $user
        );

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'text/vtt',
            ]);
    }
}
