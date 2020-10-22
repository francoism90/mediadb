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
     * @return CollectionResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Collection $collection)
    {
        if (!$collection->delete()) {
            return response()->json([], 500);
        }

        return new CollectionResource($collection);
    }
}
