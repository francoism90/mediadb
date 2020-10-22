<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\VideoSaved;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\SaveRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\Video\SaveService;

class SaveController extends Controller
{
    /**
     * @var SaveService
     */
    protected $saveService;

    public function __construct(SaveService $saveService)
    {
        $this->saveService = $saveService;
    }

    /**
     * @param SaveRequest $request
     * @param Video       $video
     *
     * @return VideoResource
     */
    public function __invoke(SaveRequest $request, Video $video)
    {
        $this->saveService->sync(
            auth()->user(),
            $video,
            $request->input('collections', []),
            $request->isMethod('put')
        );

        event(new VideoSaved($video));

        return new VideoResource($video);
    }
}
