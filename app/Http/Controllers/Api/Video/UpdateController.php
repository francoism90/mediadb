<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\Tag\SyncService as TagSyncService;

class UpdateController extends Controller
{
    /**
     * @var TagSyncService
     */
    protected $tagSyncService;

    public function __construct(TagSyncService $tagSyncService)
    {
        $this->tagSyncService = $tagSyncService;
    }

    /**
     * @param UpdateRequest $request
     * @param Video         $video
     *
     * @return VideoResource
     */
    public function __invoke(UpdateRequest $request, Video $video)
    {
        $video->setTranslation('name', locale(), $request->input('name', $video->name))
              ->setTranslation('overview', locale(), $request->input('overview', $video->overview))
              ->save();

        $video->setStatus($request->input('status', 'public'));

        $this->tagSyncService->sync(
            $video,
            $request->input('tags')
        );

        notify([
            'message' => "{$video->name} has been updated.",
            'type' => 'positive',
        ]);

        return new VideoResource($video);
    }
}
