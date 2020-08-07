<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\FrameshotRequest;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;

class FrameshotController extends Controller
{
    /**
     * @param FrameshotRequest $request
     * @param Media            $model
     *
     * @return JsonResponse
     */
    public function __invoke(
        FrameshotRequest $request,
        Media $model
    ) {
        // Set as custom property
        $model->setCustomProperty('frameshot', $request->input('timecode'))
              ->save();

        // Regenerate the thumbnail
        CreateThumbnail::dispatch($model)->onQueue('media');

        return response()->json();
    }
}
