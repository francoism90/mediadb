<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Video;
use App\Services\VideoStreamService;
use Illuminate\Http\RedirectResponse;

class StreamController extends Controller
{
    public function __construct(
        protected VideoStreamService $videoStreamService
    ) {
    }

    /**
     * @param Video $video
     * @param User  $user
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function __invoke(Video $video, User $user): RedirectResponse
    {
        $streamUrl = $this->videoStreamService->getMappingUrl($video, $user);

        return redirect($streamUrl);
    }
}
