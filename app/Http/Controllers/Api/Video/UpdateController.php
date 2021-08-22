<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\TagService;

class UpdateController extends Controller
{
    public function __invoke(UpdateRequest $request, Video $video): VideoResource
    {
        $locale = app()->getLocale();

        $video
            ->setTranslation('name', $locale, $request->input('name', $video->name))
            ->setTranslation('overview', $locale, $request->input('overview', $video->overview))
            ->setAttribute('status', $request->input('status', $video->status))
            ->setAttribute('episode_number', $request->input('episode_number', $video->episode_number))
            ->setAttribute('season_number', $request->input('season_number', $video->season_number))
            ->save();

        TagService::sync($video, $request->input('tags', []));

        event(new HasBeenUpdated($video));

        return new VideoResource($video);
    }
}
