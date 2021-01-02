<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Services\VideoStreamService;

class StreamController extends Controller
{
    /**
     * @var VideoStreamService
     */
    protected VideoStreamService $videoStreamService;

    public function __construct(VideoStreamService $videoStreamService)
    {
        $this->videoStreamService = $videoStreamService;
    }

    /**
     * @param Video $video
     * @param User  $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Video $video, User $user)
    {
        $streamUrl = $this->videoStreamService->getMappingUrl($video, $user);

        return redirect($streamUrl);
    }
}
