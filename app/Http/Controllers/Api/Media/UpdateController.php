<?php

namespace App\Http\Controllers\Api\Media;

use App\Actions\Media\UpdateMediaDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\Media\UpdateRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Media $media): MediaResource
    {
        app(UpdateMediaDetails::class)(
            $media,
            $request->validated()
        );

        $media->refresh();

        return new MediaResource($media);
    }
}
