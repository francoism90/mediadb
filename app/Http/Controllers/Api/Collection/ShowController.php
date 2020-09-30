<?php

namespace App\Http\Controllers\Api\Collection;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;

class ShowController extends Controller
{
    /**
     * @param Collection $collection
     *
     * @return CollectionResource
     */
    public function __invoke(Collection $collection)
    {
        $collection->recordActivity('viewed');
        $collection->recordView('view_count', now()->addYear());

        return new CollectionResource(
            $collection
                ->load(['model', 'tags', 'videos'])
                ->append([
                    'item_count',
                    'thumbnail_url',
                ])
        );
    }
}
