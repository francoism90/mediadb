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
    public function __invoke(Video $video)
    {
        $video->recordActivity('viewed');
        $video->recordView('view_count', now()->addYear());

        return new VideoResource(
            $video
                ->load('collections', 'tags')
                ->append([
                    'is_favorited',
                    'is_liked',
                    'tracks',
                    'bitrate',
                    'codec_name',
                    'duration',
                    'height',
                    'status',
                    'overview',
                    'sprite_url',
                    'sprite',
                    'stream_url',
                    'thumbnail_url',
                    'titles',
                    'width',
                ])
        );
    }
}
