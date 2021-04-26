<?php

namespace App\Http\Controllers\Api\Media;

use App\Events\Media\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\ThumbnailRequest;
use App\Models\Media;
use Illuminate\Http\JsonResponse;

class UpdateController extends Controller
{
    /**
     * @param UpdateRequest $request
     * @param Media         $media
     *
     * @return void
     */
    public function __invoke(ThumbnailRequest $request, Media $media): JsonResponse
    {
        $timecode = $request->input('timecode');

        logger($timecode);

        return json()->response();
    }
}
