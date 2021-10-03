<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\CreateSpriteTrack;
use App\Http\Controllers\Controller;
use App\Models\Video;

class SpriteController extends Controller
{
    public function __invoke(Video $video)
    {
        $sections = app(CreateSpriteTrack::class)($video);

        return response()
            ->view('vtt.json', compact('sections'))
            ->header('Content-Type', 'text/vtt');
    }
}
