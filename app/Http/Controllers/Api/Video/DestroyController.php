<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\DeleteVideo;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class DestroyController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        $this->authorize('delete', $video);

        app(DeleteVideo::class)($video);

        return new VideoResource($video);
    }
}
