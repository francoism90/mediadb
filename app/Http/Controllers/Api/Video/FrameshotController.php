<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\FrameshotRequest;
use App\Http\Resources\VideoResource;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Video;

class FrameshotController extends Controller
{
    /**
     * @param FrameshotRequest $request
     * @param Video            $video
     *
     * @return VideoResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(FrameshotRequest $request, Video $video)
    {
        $media = $video->getFirstMedia('clip');

        if (!$media) {
            return response()->json([], 500);
        }

        // Set frameshot attribute
        $media
            ->setCustomProperty('frameshot', $request->input('timecode'))
            ->save();

        // Dispatch thumbnail job
        CreateThumbnail::dispatch($media)->onQueue('media');

        event(new HasBeenUpdated($video));

        return new VideoResource($video);
    }
}
