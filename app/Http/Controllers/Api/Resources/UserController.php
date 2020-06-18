<?php

namespace App\Http\Controllers\Api\Resources;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Support\QueryBuilder\Filters\SimpleQueryFilter;
use App\Support\QueryBuilder\Sorts\RecommendedSorter;
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
        $defaultSort = AllowedSort::custom('recommended', new RecommendedSorter());

        $query = QueryBuilder::for(User::class)
            ->allowedIncludes('media')
            ->allowedFilters([
                AllowedFilter::custom('query', new SimpleQueryFilter())->ignore(null, '*', '#'),
            ])
            ->allowedSorts([
                $defaultSort,
                AllowedSort::field('name'),
                AllowedSort::field('created_at'),
            ])
            ->defaultSort($defaultSort)
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
        // Tracking
        $user->recordActivity('show');
        $user->recordView('view_count', now()->addYear());

        return new UserResource($user);
    }
}
