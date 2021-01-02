<?php

namespace App\Http\Controllers\Api\Video;

use App\Events\Video\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\UpdateRequest;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Services\CollectionService;
use App\Services\TagService;

class UpdateController extends Controller
{
    /**
     * @var CollectionService
     */
    protected CollectionService $collectionService;

    /**
     * @var TagService
     */
    protected TagService $tagService;

    public function __construct(
        CollectionService $collectionService,
        TagService $tagService
    ) {
        $this->collectionService = $collectionService;
        $this->tagService = $tagService;
    }

    /**
     * @param UpdateRequest $request
     * @param Video         $video
     *
     * @return VideoResource
     */
    public function __invoke(UpdateRequest $request, Video $video)
    {
        $locale = app()->getLocale();

        $video->setTranslation('name', $locale, $request->input('name', $video->name))
              ->setTranslation('overview', $locale, $request->input('overview', $video->overview))
              ->save();

        $this->collectionService->sync(
            $video,
            $request->input('collections', [])
        );

        $this->tagService->sync(
            $video,
            $request->input('tags', [])
        );

        $video->setStatus($request->input('status', $video->status));

        event(new HasBeenUpdated($video));

        return new VideoResource($video);
    }
}
