<?php

namespace App\Http\Controllers\Api\Video;

use App\Http\Controllers\Controller;
use App\Http\Requests\Video\IndexRequest;
use App\Http\Resources\VideoCollection;
use App\Models\Video;
use App\Services\MeiliSearchService;
use Illuminate\Http\Resources\Json\ResourceCollection;
use MeiliSearch\Endpoints\Indexes;

class IndexController extends Controller
{
    public function __construct(
        protected MeiliSearchService $searchService
    )
    {}

    public function __invoke(IndexRequest $request): ResourceCollection
    {
        $this->authorize('viewAny', Video::class);

        $items = app(MeiliSearchService::class)
            ->subject(Video::class)
            ->for($request)
            ->add('query', $request->input('filter.query'))
            ->sort($request->input('sort'))
            ->paginate();

        // $request->validated();

        // $collect = Video::search($request->input('query', '*'),
        //     function (Indexes $meilisearch, $query, $options) use ($request) {



        //         logger($options);
        //         // $options['q'] = ['duration:asc'];
        //         // $options['sort'] = ['duration:asc'];
        //         $options['sort'] = [$request->input('sort')];

        //         return $meilisearch->search($query, $options);
        //     })
        //     ->simplePaginate(24);

        return new VideoCollection($items);
    }
}
