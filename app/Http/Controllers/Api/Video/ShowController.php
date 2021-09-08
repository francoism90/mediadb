<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Jobs\Media\Process;
use App\Models\Video;

class ShowController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        auth()?->user()?->view($video);

        Process::dispatch($video->getFirstMedia('clip'));

        return new VideoResource(
            $video
                ->load('tags', 'viewers')
                ->append([
                    'clip',
                    'favorite',
                    'following',
                    'views',
                    'poster_url',
                    'sprite_url',
                    'vod_url',
                ])
        );
    }
}
