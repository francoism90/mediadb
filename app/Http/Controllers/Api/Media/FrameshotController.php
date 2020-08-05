<?php

namespace App\Http\Controllers\Api\Media;

use App\Actions\Media\CreateThumbnail;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\FrameshotRequest;
use App\Models\Media;

class FrameshotController extends Controller
{
    /**
     * @param FrameshotRequest $request
     * @param Media            $model
     * @param CreateThumbnail  $action
     *
     * @return JsonResponse
     */
    public function __invoke(
        FrameshotRequest $request,
        Media $model,
        CreateThumbnail $action
    ) {
        // Set as custom property
        $model->setCustomProperty('frameshot', $request->input('timecode'))
              ->save();

        // Regenerate the thumbnail
        $action->onQueue('media')->execute($model);

        return response()->json();
    }
}
