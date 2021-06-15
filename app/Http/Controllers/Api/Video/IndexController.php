<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Support\ModelFilters\VideoFilter;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    public function __invoke(Request $request): ResourceCollection
    {
        $videos = Video::filter($request->all(), VideoFilter::class)->simplePaginateFilter();

        return VideoResource::collection($videos);
    }
}
