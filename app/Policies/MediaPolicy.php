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
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function view(?User $user, Media $media)
    {
        if ($media->hasGeneratedConversion('preview')) {
            return true;
        }

        // Visitors cannot view unpublished items
        if (null === $user) {
            return false;
        }

        return $user->id === $media->model->id;
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
        if ($user->can('create media')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function update(User $user, Media $media)
    {
        if ($user->can('edit media')) {
            return true;
        }

        return $user->id === $media->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function delete(User $user, Media $media)
    {
        if ($user->can('delete media')) {
            return true;
        }

        return $user->id === $media->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function restore(User $user, Media $media)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User  $user
     * @param \App\Models\Media $media
     *
     * @return mixed
     */
    public function forceDelete(User $user, Media $media)
    {
    }
}
