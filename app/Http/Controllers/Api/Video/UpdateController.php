<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\UpdateVideoDetails;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Video $video): VideoResource
    {
        $this->authorize('update', $video);

        app(UpdateVideoDetails::class)(
            $video,
            $request->validated()
        );

        return new VideoResource($video);
    }
}
