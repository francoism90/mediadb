<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
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
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function view(?User $user, Collection $collection)
    {
        if ($collection->latestStatus(['published'])->exists()) {
            return true;
        }

        // Visitors cannot view unpublished items
        if (null === $user) {
            return false;
        }

        return $user->id === $collection->model->id;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('create collections')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function update(User $user, Collection $collection)
    {
        if ($user->can('edit collections')) {
            return true;
        }

        return $user->id === $collection->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function delete(User $user, Collection $collection)
    {
        if ($user->can('delete collections')) {
            return true;
        }

        return $user->id === $collection->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function restore(User $user, Collection $collection)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return mixed
     */
    public function forceDelete(User $user, Collection $collection)
    {
    }
}
