<?php

namespace App\Http\Controllers\Api\Media;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\SaveRequest;
use App\Http\Resources\MediaResource;
use App\Models\Media;
use App\Services\MediaCollectionService;

class SaveController extends Controller
{
    protected $mediaCollectionService;

    public function __construct(MediaCollectionService $mediaCollectionService)
    {
        $this->mediaCollectionService = $mediaCollectionService;
    }

    /**
     * @param Media $media
     *
     * @return MediaResource
     */
    public function __invoke(SaveRequest $request, Media $media)
    {
        $this->mediaCollectionService->sync(
            auth()->user(),
            $media,
            collect($request->input('collections', []))
        );

        return new MediaResource($media);
    }
}
