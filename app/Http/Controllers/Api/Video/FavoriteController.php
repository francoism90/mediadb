<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\User\MarkModelAsFavorite;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\FavoriteRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;

class FavoriteController extends Controller
{
    public function __invoke(FavoriteRequest $request, Video $video): VideoResource
    {
        $this->authorize('view', $video);

        app(MarkModelAsFavorite::class)(
            auth()->user(),
            $video,
            $request->boolean('favorite')
        );

        $video->refresh();

        return new VideoResource(
            $video->append('favorite')
        );
    }
}
