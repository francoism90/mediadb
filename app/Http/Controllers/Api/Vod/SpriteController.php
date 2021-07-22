<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\SpriteService;
use Illuminate\Http\Response;

class SpriteController extends Controller
{
    public function __construct(
        protected SpriteService $spriteService
    ) {
    }

    public function __invoke(Video $video): Response
    {
        $contents = $this->spriteService->create($video);

        return response($contents)
            ->withHeaders([
                'Content-Type' => 'text/vtt',
            ]);
    }
}
