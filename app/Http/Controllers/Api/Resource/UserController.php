<?php

namespace App\Http\Controllers\Api\Resource;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\QueryBuilder\Filters\QueryFilter;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends Controller
{
    /**
     * @return UserResource
     */
    public function index()
    {
        $query = QueryBuilder::for(User::class)
            ->allowedIncludes('tags')
            ->allowedFilters([
                AllowedFilter::custom('query', new QueryFilter()),
            ])
            ->allowedSorts([
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
            ])
            ->jsonPaginate();

        return UserResource::collection($query);
    }

    /**
     * @param User $user
     *
     * @return UserResource
     */
    public function show(User $user)
    {
        $user->recordView('users');

        return new UserResource($user);
    }
}
