<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
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
        if (null === $user) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function view(?User $user, Video $model): bool
    {
        if (null === $user) {
            return false;
        }

        if ($model->latestStatus(['private'])->exists()) {
            return false;
        }

        return $user->id === $model->model->id;
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
        if ($user->can('create video')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function update(User $user, Video $model): bool
    {
        if ($user->can('edit video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function delete(User $user, Video $model): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function restore(User $user, Video $model): bool
    {
        if ($user->can('restore video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User  $user
     * @param Video $model
     *
     * @return bool
     */
    public function forceDelete(User $user, Video $model): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }
}
