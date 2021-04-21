<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class ShowController extends Controller
{
    /**
     * @param Video $video
     *
     * @return VideoResource
     */
    public function __invoke(Video $video): VideoResource
    {
        $video->recordActivity('viewed');
        $video->recordView('view_count', now()->addYear());

        return new VideoResource(
            $video
                ->load('tags')
                ->append([
                    'clip',
                    'tracks',
                ])
        );
    }
}
