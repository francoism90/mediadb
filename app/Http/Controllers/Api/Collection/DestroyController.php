<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;

class DestroyController extends Controller
{
    /**
     * @param Collection $collection
     *
     * @return CollectionResource
     */
    public function __invoke(Collection $collection)
    {
        $this->authorize('delete', $collection);

        if ($collection->delete()) {
            return new CollectionResource($collection);
        }

        return response()->json([], 500);
    }
}
