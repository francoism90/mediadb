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
     * @return mixed
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
     * @return mixed
     */
    public function view(?User $user, Video $video)
    {
        if ($video->latestStatus(['published'])->exists()) {
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
     * @return mixed
     */
    public function create(User $user)
    {
        if ($user->can('create video')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return mixed
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
     * @return mixed
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
     * @return mixed
     */
    public function restore(User $user, Video $video)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Video $video
     *
     * @return mixed
     */
    public function forceDelete(User $user, Video $video)
    {
    }
}
