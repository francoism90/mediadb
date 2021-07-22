<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class ShowController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        auth()?->user()?->view($video);

        return new VideoResource(
            $video
                ->load('tags', 'viewers')
                ->append([
                    'clip',
                    'favorite',
                    'following',
                    'views',
                ])
        );
    }
}
