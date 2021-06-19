<?php

namespace App\Http\Controllers\Api\Media;

use App\Events\Media\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Jobs\Media\CreateThumbnail;
use App\Models\Media;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Media $media): MediaResource
    {
        $media
            ->setCustomProperty('thumbnail', $request->input('thumbnail', $media->thumbnail))
            ->save();

        CreateThumbnail::dispatch($media)->onQueue('media');

        event(new HasBeenUpdated($media));

        return new MediaResource($media);
    }
}
