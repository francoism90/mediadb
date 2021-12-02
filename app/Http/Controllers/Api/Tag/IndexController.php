<?php

namespace App\Http\Controllers\Api\Tag;

use App\Actions\Tag\GetTagsIndex;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tag\IndexRequest;
use App\Http\Resources\TagCollection;
use App\Models\Tag;
use Illuminate\Http\Resources\Json\ResourceCollection;

class IndexController extends Controller
{
    public function __invoke(IndexRequest $request): ResourceCollection
    {
        $this->authorize('viewAny', Tag::class);

        $items = app(GetTagsIndex::class)($request);

        $pageSize = $request->input('size', 24);

        return new TagCollection(
            $items->simplePaginate($pageSize)
        );
    }
}
