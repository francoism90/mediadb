<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class DestroyController extends Controller
{
    /**
     * @param Video $video
     *
     * @return VideoResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Video $video)
    {
        if (!$video->delete()) {
            return response()->json([], 500);
        }

        event(new HasBeenDeleted($video));

        return new VideoResource($video);
    }
}
