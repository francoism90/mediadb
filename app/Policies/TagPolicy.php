<?php

namespace App\Policies;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        if (null === $user) {
            return false;
        }

        return true;
    }

    public function view(?User $user, Tag $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        if ($user->can('create tag')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Tag $model): bool
    {
        if ($user->can('edit tag')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Tag $model): bool
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }

    public function restore(User $user, Tag $model): bool
    {
        if ($user->can('restore tag')) {
            return true;
        }

        return false;
    }

    public function forceDelete(User $user, Tag $model): bool
    {
        if ($user->can('delete tag')) {
            return true;
        }

        return false;
    }
}
