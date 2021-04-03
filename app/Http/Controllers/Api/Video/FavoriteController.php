<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenFavorited;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteController extends Controller
{
    /**
     * @param Request $request
     * @param Video   $video
     *
     * @return VideoResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Video $video): VideoResource | JsonResource
    {
        $user = auth()->user();

        if ($request->isMethod('delete')) {
            $user->unfavorite($video);
        } else {
            $user->favorite($video);
        }

        event(new HasBeenFavorited($video));

        return new VideoResource($video);
    }
}
