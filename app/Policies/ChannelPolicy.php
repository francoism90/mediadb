<?php

namespace App\Policies;

use App\Models\Channel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChannelPolicy
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
     * @param \App\Models\User    $user
     * @param \App\Models\Channel $channel
     *
     * @return mixed
     */
    public function view(?User $user, Channel $channel)
    {
        if ($channel->latestStatus(['published'])->exists()) {
            return true;
        }

        // Visitors cannot view unpublished items
        if (null === $user) {
            return false;
        }

        return $user->id === $channel->model->id;
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
        if ($user->can('create channels')) {
            return true;
        }
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\Channel $channel
     *
     * @return mixed
     */
    public function update(User $user, Channel $channel)
    {
        if ($user->can('edit channels')) {
            return true;
        }

        return $user->id === $channel->model->id;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\Channel $channel
     *
     * @return mixed
     */
    public function delete(User $user, Channel $channel)
    {
        if ($user->can('delete channels')) {
            return true;
        }

        return $user->id === $channel->model->id;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\Channel $channel
     *
     * @return mixed
     */
    public function restore(User $user, Channel $channel)
    {
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User    $user
     * @param \App\Models\Channel $channel
     *
     * @return mixed
     */
    public function forceDelete(User $user, Channel $channel)
    {
    }
}
