<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\User\MarkModelAsFollowing;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\FollowRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class FollowController extends Controller
{
    public function __invoke(FollowRequest $request, Video $video): VideoResource
    {
        $this->authorize('view', $video);

        app(MarkModelAsFollowing::class)(
            auth()->user(),
            $video,
            $request->boolean('following')
        );

        $video->refresh();

        return new VideoResource(
            $video->append('following')
        );
    }
}
