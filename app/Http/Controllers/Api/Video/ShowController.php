<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\User\MarkModelAsViewed;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class ShowController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        $this->authorize('view', $video);

        app(MarkModelAsViewed::class)(auth()->user(), $video, true);

        return new VideoResource(
            $video
                ->load('tags', 'viewers')
                ->append([
                    'clips',
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
