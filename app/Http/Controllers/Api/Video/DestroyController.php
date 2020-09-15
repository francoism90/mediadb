<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class DestroyController extends Controller
{
    /**
     * @param Video $video
     *
     * @return VideoResource
     */
    public function __invoke(Video $video)
    {
        $this->authorize('delete', $video);

        if ($video->delete()) {
            return new VideoResource($video);
        }

        return response()->json([], 500);
    }
}
