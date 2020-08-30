<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\Media\SpriteTrackService;

class SpriteController extends Controller
{
    /**
     * @var SpriteTrackService
     */
    protected $spriteTrackService;

    public function __construct(SpriteTrackService $spriteTrackService)
    {
        $this->spriteTrackService = $spriteTrackService;
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

        $contents = $this->spriteTrackService->getContents(
            $media,
            $user
        );

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'text/vtt',
            ]);
    }
}
