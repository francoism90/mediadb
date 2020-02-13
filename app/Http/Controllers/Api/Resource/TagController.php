<?php

namespace App\Http\Controllers\Api\Resource;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TagController extends Controller
{
    public function index()
    {
        $query = QueryBuilder::for(Tag::class)
            ->defaultSort('order_column')
            ->allowedFilters([
                AllowedFilter::custom('query', new SimpleQueryFilter()),
            ])
            ->paginate(Tag::count());

        return TagResource::collection($query);
    }
}
