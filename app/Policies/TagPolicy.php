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
     * @param User $user
     * @param Tag  $model
     *
     * @return bool
     */
    public function view(?User $user, Tag $model): bool
    {
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
        if ($user->can('create tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Tag  $model
     *
     * @return bool
     */
    public function update(User $user, Tag $model): bool
    {
        if ($user->can('edit tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Tag  $model
     *
     * @return bool
     */
    public function delete(User $user, Tag $model): bool
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Tag  $model
     *
     * @return bool
     */
    public function restore(User $user, Tag $model): bool
    {
        if ($user->can('restore tag')) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Tag  $model
     *
     * @return bool
     */
    public function forceDelete(User $user, Tag $model): bool
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }
}
