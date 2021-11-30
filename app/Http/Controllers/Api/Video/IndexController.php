<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\IndexRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use App\Services\MeiliSearchService;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    public function __invoke(IndexRequest $request): ResourceCollection
    {
        $this->authorize('viewAny', Video::class);

        $items = app(MeiliSearchService::class)
            ->subject(Video::class)
            ->for($request)
            ->query($request->input('filter.query'))
            ->filter('tags', $request->input('filter.tags'))
            ->sort($request->input('sort'))
            ->limit(24)
            ->paginate();

        return new VideoCollection($items);
    }
}
