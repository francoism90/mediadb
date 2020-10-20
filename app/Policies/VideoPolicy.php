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
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return bool
     */
    public function view(?User $user, Video $video)
    {
        if ($video->latestStatus(['public'])->exists()) {
            return true;
        }

        // Visitors cannot view private items
        if (null === $user) {
            return false;
        }

        return $user->id === $video->model->id;
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
        if ($user->can('create video')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return bool
     */
    public function update(User $user, Video $video)
    {
        if ($user->can('edit video')) {
            return true;
        }

        return $user->id === $video->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return bool
     */
    public function delete(User $user, Video $video)
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $video->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return bool
     */
    public function restore(User $user, Video $video)
    {
        if ($user->can('restore video')) {
            return true;
        }

        return $user->id === $video->model->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return bool
     */
    public function forceDelete(User $user, Video $video)
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $video->model->id;
    }
}
