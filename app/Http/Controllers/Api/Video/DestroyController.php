<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class DestroyController extends Controller
{
    public function __invoke(Video $video): VideoResource
    {
        abort_if(!$video->delete(), 500);

        event(new HasBeenDeleted($video));

        return new VideoResource($video);
    }
}
