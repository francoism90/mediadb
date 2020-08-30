<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\SaveRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VideoSaveService;

class SaveController extends Controller
{
    protected $videoSaveService;

    public function __construct(VideoSaveService $videoSaveService)
    {
        $this->videoSaveService = $videoSaveService;
    }

    /**
     * @param SaveRequest $request
     * @param Video       $video
     *
     * @return VideoResource
     */
    public function __invoke(SaveRequest $request, Video $video)
    {
        $this->videoSaveService->sync(
            auth()->user(),
            $video,
            collect($request->input('collections', [])),
            $request->isMethod('put')
        );

        return new VideoResource($video);
    }
}
