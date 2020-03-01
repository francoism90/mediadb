<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\QueryBuilder\Filters\QueryFilter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * @return UserResource
     */
    public function index(Request $request)
    {
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $query = QueryBuilder::for(User::class)
            ->allowedIncludes('tags')
            ->allowedFilters([
                AllowedFilter::exact('id', 'slug')->ignore(null, '*'),
                AllowedFilter::custom('query', new QueryFilter())->ignore(null, '*'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
            ])
            ->defaultSort($defaultSort);

        if ($request->has('page.size')) {
            return UserResource::collection($query->jsonPaginate());
        }

        return new UserResource($query->first());
    }
}
