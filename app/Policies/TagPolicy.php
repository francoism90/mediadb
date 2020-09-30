<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $tag
     *
     * @return bool
     */
    public function view(?User $user, Tag $tag)
    {
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
        if ($user->can('create tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $tag
     *
     * @return bool
     */
    public function update(User $user, Tag $tag)
    {
        if ($user->can('edit tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $tag
     *
     * @return bool
     */
    public function delete(User $user, Tag $tag)
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $tag
     *
     * @return bool
     */
    public function restore(User $user, Tag $tag)
    {
        if ($user->can('restore tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\User $user
     * @param \App\Models\Tag  $tag
     *
     * @return bool
     */
    public function forceDelete(User $user, Tag $tag)
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }
}
