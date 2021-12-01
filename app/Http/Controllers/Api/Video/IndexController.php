<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\GetVideosIndex;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\IndexRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    public function __invoke(IndexRequest $request): ResourceCollection
    {
        $this->authorize('viewAny', Video::class);

        $items = app(GetVideosIndex::class)($request);

        $pageSize = $request->input('size', 24);

        return new VideoCollection(
            $items->simplePaginate($pageSize)
        );
    }
}
