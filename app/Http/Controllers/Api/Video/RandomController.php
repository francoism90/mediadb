<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class RandomController extends Controller
{
    public function __invoke(): VideoResource
    {
        $this->authorize('viewAny', Video::class);

        $video = Video::inRandomOrder()->firstOrFail();

        return new VideoResource(
            $video
        );
    }
}
