<?php

namespace App\Http\Controllers\Api\Tag;

use App\Events\Tag\HasBeenDeleted;
use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class DestroyController extends Controller
{
    public function __invoke(Tag $tag): TagResource
    {
        abort_if(!$tag->delete(), 500);

        event(new HasBeenDeleted($tag));

        return new TagResource($tag);
    }
}
