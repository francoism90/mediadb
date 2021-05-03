<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenFavorited;
use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    /**
     * @param Request $request
     * @param Video   $video
     *
     * @return VideoResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Request $request, Video $video): VideoResource | JsonResource
    {
        $this->userService->favorite(
            auth()->user(),
            $video,
            $request->isMethod('delete')
        );

        event(new HasBeenFavorited($video));

        return new VideoResource($video);
    }
}
