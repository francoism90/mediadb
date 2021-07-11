<?php

namespace App\Http\Controllers\Api\Tag;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class ShowController extends Controller
{
    public function __invoke(Tag $tag): TagResource
    {
        return new TagResource(
            $tag->append([
                'views',
            ])
        );
    }
}
