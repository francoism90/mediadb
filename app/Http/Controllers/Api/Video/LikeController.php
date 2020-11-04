<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\VideoHasBeenLiked;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * @param Request $request
     * @param Video   $video
     *
     * @return VideoResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Video $video)
    {
        $user = auth()->user();

        if ($request->isMethod('delete')) {
            $user->unlike($video);
        } else {
            $user->like($video);
        }

        event(new VideoHasBeenLiked($video));

        return new VideoResource($video);
    }
}
