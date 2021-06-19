<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Models\User;
use App\Services\MediaSpriteService;
use Illuminate\Http\Response;

class SpriteController extends Controller
{
    public function __construct(
        protected MediaSpriteService $mediaSpriteService
    ) {
    }

    public function __invoke(Media $media, User $user): Response
    {
        $contents = $this->mediaSpriteService->create($media);

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'text/vtt',
            ]);
    }
}
