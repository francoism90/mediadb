<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\User\MarkModelAsViewed;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Jobs\Media\Process;
use App\Models\Video;

class ShowController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        app(MarkModelAsViewed::class)->execute(auth()->user(), $video);

        // Process::dispatchNow($video->getFirstMedia('clips'));

        return new VideoResource(
            $video
                ->load('tags', 'viewers')
                ->append([
                    'clip',
                    'favorite',
                    'following',
                    'views',
                    'dash_url',
                    'poster_url',
                    'sprite_url',
                ])
        );
    }
}
