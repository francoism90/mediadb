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
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(
        FrameshotRequest $request,
        Video $video
    ) {
        $clip = $video->getFirstMedia('clips');

        if (!$clip) {
            return response()->json([], 500);
        }

        $clip
            ->setCustomProperty('frameshot', $request->input('timecode'))
            ->save();

        CreateThumbnail::dispatch($clip)->onQueue('media');

        notify([
            'message' => "{$video->name} has been updated.",
            'type' => 'positive',
        ]);
    }
}
