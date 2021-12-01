<?php

namespace App\Http\Controllers\Api\Video;

use App\Actions\Video\GetSimilarVideos;
use App\Http\Controllers\Controller;
use App\Http\Requests\Video\IndexRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use App\Services\MeiliSearchService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class SimilarController extends Controller
{
    public function __invoke(IndexRequest $request, Video $video): ResourceCollection
    {
        $this->authorize('viewAny', Video::class);

        $ids = app(GetSimilarVideos::class)($video)->pluck('id')->toArray();
        // logger($ids);

        $items = app(MeiliSearchService::class)
            ->subject(Video::class)
            ->filter('id', $ids, 'OR')
            ->limit($request->input('size', 24))
            ->paginate();

        return new VideoCollection($items);
    }
}
