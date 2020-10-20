<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\UpdateRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
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
     * @param Collection    $collection
     *
     * @return CollectionResource
     */
    public function __invoke(UpdateRequest $request, Collection $collection)
    {
        $collection->setTranslation('name', locale(), $request->input('name', $collection->name))
                   ->setTranslation('overview', locale(), $request->input('overview', $collection->overview))
                   ->save();

        $collection->setStatus($request->input('status', 'public'));

        $this->tagSyncService->sync(
            $collection,
            $request->input('tags')
        );

        notify([
            'message' => "{$collection->name} has been updated.",
            'type' => 'positive',
        ]);

        return new CollectionResource($collection);
    }
}
