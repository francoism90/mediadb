<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\GetSimilarVideos;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\IndexRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SimilarController extends Controller
{
    public function __invoke(IndexRequest $request, Video $video): ResourceCollection
    {
        $this->authorize('viewAny', Video::class);

        $items = app(GetSimilarVideos::class)($video);

        $pageSize = $request->input('size', 24);

        return new VideoCollection(
            $items->simplePaginate($pageSize)
        );
    }
}
