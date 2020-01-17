<?php

namespace App\Http\Controllers\Api\Resource;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Filters\Tag\MediaFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Tag::class)
            ->defaultSort('order_column')
            ->allowedFilters([
                AllowedFilter::custom('feed', new MediaFilter()),
                AllowedFilter::custom('query', new QueryFilter()),
            ])
            ->get();

        return TagResource::collection($query);
    }
}
