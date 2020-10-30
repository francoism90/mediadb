<?php

namespace App\Http\Controllers\Api\Tag;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;

class ShowController extends Controller
{
    /**
     * @param Tag $tag
     *
     * @return TagResource
     */
    public function __invoke(Tag $tag)
    {
        // $tag->recordActivity('viewed');
        $tag->recordView('view_count', now()->addYear());

        return new TagResource(
            $tag
                ->append([
                    'views',
                    'items',
                    'collections',
                    'videos',
                    'thumbnail_url',
                ])
        );
    }
}
