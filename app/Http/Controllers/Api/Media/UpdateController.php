<?php

namespace App\Http\Controllers\Api\Media;

use App\Events\Media\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Jobs\Media\CreateThumbnail;
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
    public function __invoke(UpdateRequest $request, Media $media): MediaResource | JsonResponse
    {
        $media
            ->setCustomProperty('thumbnail', $request->input('thumbnail', $media->thumbnail))
            ->save();

        CreateThumbnail::dispatch($media)->onQueue('media');

        event(new HasBeenUpdated($media));

        return new MediaResource($media);
    }
}
