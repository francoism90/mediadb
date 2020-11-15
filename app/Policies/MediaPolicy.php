<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
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
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $model
     *
     * @return bool
     */
    public function view(?User $user, Media $model)
    {
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
        if ($user->can('create media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $model
     *
     * @return bool
     */
    public function update(User $user, Media $model)
    {
        if ($user->can('edit media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $model
     *
     * @return bool
     */
    public function delete(User $user, Media $model)
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $model
     *
     * @return bool
     */
    public function restore(User $user, Media $model)
    {
        if ($user->can('restore media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $model
     *
     * @return bool
     */
    public function forceDelete(User $user, Media $model)
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }
}
