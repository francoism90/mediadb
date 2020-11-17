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
    public function viewAny(?User $user)
    {
        if (null === $user) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User       $user
     * @param \App\Models\Collection $model
     *
     * @return bool
     */
    public function view(?User $user, Collection $model)
    {
        if (null === $user) {
            return false;
        }

        if ($model->latestStatus(['private'])->exists()) {
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
     * @param \App\Models\Collection $model
     *
     * @return bool
     */
    public function update(User $user, Collection $model)
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
     * @param \App\Models\Collection $model
     *
     * @return bool
     */
    public function delete(User $user, Collection $model)
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
     * @param \App\Models\Collection $model
     *
     * @return bool
     */
    public function restore(User $user, Collection $model)
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
     * @param \App\Models\Collection $model
     *
     * @return bool
     */
    public function forceDelete(User $user, Collection $model)
    {
        if ($user->can('delete collections')) {
            return true;
        }

        return false;
    }
}
