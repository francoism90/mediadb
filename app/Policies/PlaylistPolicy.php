<?php

namespace App\Policies;

use App\Models\Playlist;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PlaylistPolicy
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
     * @param \App\Models\User     $user
     * @param \App\Models\Playlist $playlist
     *
     * @return mixed
     */
    public function view(?User $user, Playlist $playlist)
    {
        if ($playlist->latestStatus(['published'])->exists()) {
            return true;
        }

        // Visitors cannot view unpublished items
        if (null === $user) {
            return false;
        }

        return $user->id === $playlist->model->id;
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
        if ($user->can('create playlists')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Playlist $playlist
     *
     * @return mixed
     */
    public function update(User $user, Playlist $playlist)
    {
        if ($user->can('edit playlists')) {
            return true;
        }

        return $user->id === $playlist->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Playlist $playlist
     *
     * @return mixed
     */
    public function delete(User $user, Playlist $playlist)
    {
        if ($user->can('delete playlists')) {
            return true;
        }

        return $user->id === $playlist->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Playlist $playlist
     *
     * @return mixed
     */
    public function restore(User $user, Playlist $playlist)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User     $user
     * @param \App\Models\Playlist $playlist
     *
     * @return mixed
     */
    public function forceDelete(User $user, Playlist $playlist)
    {
    }
}
