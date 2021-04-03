<?php

namespace App\Http\Controllers\Api\Tag;

use App\Events\Tag\HasBeenDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class DestroyController extends Controller
{
    /**
     * @param Tag $tag
     *
     * @return TagResource|\Illuminate\Http\JsonResponse
     */
    public function __invoke(Tag $tag): TagResource | JsonResponse
    {
        if (!$tag->delete()) {
            return response()->json([], 500);
        }

        event(new HasBeenDeleted($tag));

        return new TagResource($tag);
    }
}
