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
        $this->authorize('update', $video);

        $video->update([
            'name' => $request->input('name'),
            'overview' => $request->input('overview'),
        ]);

        $video->setStatus($request->input('status', 'released'));

        $this->tagSyncService->sync(
            $video,
            $request->input('tags')
        );

        return new VideoResource($video);
    }
}
