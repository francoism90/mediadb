<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any collections.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the Collection.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function view(User $user, Collection $collection)
    {
        return true;
    }

    /**
     * Determine whether the user can create collections.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the Collection.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function update(User $user, Collection $collection)
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can delete the Collection.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function delete(User $user, Collection $collection)
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can restore the Collection.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function restore(User $user, Collection $collection)
    {
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can permanently delete the Collection.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function forceDelete(User $user, Collection $collection)
    {
        return $user->id === $collection->user_id;
    }
}
