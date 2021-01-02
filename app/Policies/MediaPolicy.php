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
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function view(?User $user, Media $model): bool
    {
        if (null === $user) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        if ($user->can('create media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function update(User $user, Media $model): bool
    {
        if ($user->can('edit media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function delete(User $user, Media $model): bool
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function restore(User $user, Media $model): bool
    {
        if ($user->can('restore media')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User  $user
     * @param Media $model
     *
     * @return bool
     */
    public function forceDelete(User $user, Media $model): bool
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }
}
