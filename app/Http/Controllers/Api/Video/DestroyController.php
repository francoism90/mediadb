<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\RemoveVideo;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class DestroyController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        app(RemoveVideo::class)->execute($video);

        return new VideoResource($video);
    }
}
