<?php

namespace App\Http\Controllers\Api\Vod;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\VodService;

class SpriteController extends Controller
{
    public function __invoke(Video $video)
    {
        $contents = app(VodService::class, ['model' => $video])
            ->getSpriteContents();

        return response($contents)
            ->header('Content-Type', 'text/vtt');
    }
}
