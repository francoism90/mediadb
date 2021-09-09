<?php

namespace App\Http\Controllers\Api\Vod;

use App\Actions\Vod\CreateSpriteItems;
use App\Http\Controllers\Controller;
use App\Models\Video;

class SpriteController extends Controller
{
    public function __invoke(Video $video)
    {
        $items = app(CreateSpriteItems::class)->execute($video);

        return response()
            ->view('vtt.json', compact('items'))
            ->header('Content-Type', 'text/vtt');
    }
}
