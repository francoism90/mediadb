<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\FrameshotRequest;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Video;

class FrameshotController extends Controller
{
    /**
     * @param FrameshotRequest $request
     * @param Video            $video
     *
     * @return JsonResponse
     */
    public function __invoke(
        FrameshotRequest $request,
        Video $video
    ) {
        $this->authorize('update', $video);

        optional($video->getFirstMedia('clip'), function ($clip) use ($request) {
            $clip
                ->setCustomProperty('frameshot', $request->input('timecode'))
                ->save();

            CreateThumbnail::dispatch($clip)->onQueue('media');
        });

        return response()->json();
    }
}
