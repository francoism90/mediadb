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
     * @return bool
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
     * @return bool
     */
    public function view(?User $user, Collection $collection)
    {
        if ($collection->latestStatus(['public'])->exists()) {
            return true;
        }

        // Visitors cannot view private items
        if (null === $user) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\User $user
     *
     * @return bool
     */
    public function create(User $user)
    {
        if ($user->can('create collections')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return bool
     */
    public function update(User $user, Collection $collection)
    {
        if ($user->can('edit collections')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return bool
     */
    public function delete(User $user, Collection $collection)
    {
        if ($user->can('delete collections')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return bool
     */
    public function restore(User $user, Collection $collection)
    {
        if ($user->can('restore collections')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $collection
     *
     * @return bool
     */
    public function forceDelete(User $user, Collection $collection)
    {
        if ($user->can('delete collections')) {
            return true;
        }

        return false;
    }
}
