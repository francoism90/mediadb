<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\CreateSpriteItems;
use App\Http\Controllers\Controller;
use App\Models\Video;

class SpriteController extends Controller
{
    public function __invoke(Video $video)
    {
        $items = app(CreateSpriteItems::class)($video);

        return response()
            ->view('vtt.json', compact('items'))
            ->header('Content-Type', 'text/vtt');
    }
}
