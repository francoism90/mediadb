<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\SaveRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\VideoCollectionService;

class SaveController extends Controller
{
    protected $videoCollectionService;

    public function __construct(VideoCollectionService $videoCollectionService)
    {
        $this->videoCollectionService = $videoCollectionService;
    }

    /**
     * @param SaveRequest $request
     * @param Video       $video
     *
     * @return VideoResource
     */
    public function __invoke(SaveRequest $request, Video $video)
    {
        $this->videoCollectionService->sync(
            auth()->user(),
            $video,
            collect($request->input('collections', [])),
            $request->isMethod('put')
        );

        return new VideoResource($video);
    }
}
