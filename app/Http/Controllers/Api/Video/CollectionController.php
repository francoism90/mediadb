<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\User;
use App\Models\Video;

class CollectionController extends Controller
{
    /**
     * @return CollectionResource
     */
    public function __invoke(Video $video)
    {
        $collections = $video
            ->collections
            ->where('model_type', User::class)
            ->where('model_id', auth()->user()->id)
            ->all();

        return CollectionResource::collection($collections);
    }
}
