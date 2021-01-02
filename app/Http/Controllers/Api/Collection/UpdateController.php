<?php

namespace App\Http\Controllers\Api\Collection;

use App\Events\Collection\HasBeenUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use App\Services\TagService;

class UpdateController extends Controller
{
    /**
     * @var TagService
     */
    protected TagService $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    /**
     * @param UpdateRequest $request
     * @param Collection    $collection
     *
     * @return CollectionResource
     */
    public function __invoke(UpdateRequest $request, Collection $collection)
    {
        $locale = app()->getLocale();

        $collection->setTranslation('name', $locale, $request->input('name', $collection->name))
                   ->setTranslation('overview', $locale, $request->input('overview', $collection->overview))
                   ->save();

        $this->tagService->sync(
            $collection,
            $request->input('tags', [])
        );

        $collection->setStatus($request->input('status', $collection->status));

        event(new HasBeenUpdated($collection));

        return new CollectionResource($collection);
    }
}
