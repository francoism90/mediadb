<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Support\ModelFilters\VideoFilter;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function __invoke(Request $request, )
    {
        $videos = Video::filter($request->all(), VideoFilter::class)->simplePaginateFilter();

        return VideoResource::collection($videos);
    }
}
