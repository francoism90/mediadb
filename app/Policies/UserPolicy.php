<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return null !== $user;
    }

    public function view(User $user): bool
    {
        return null !== $user;
    }

    public function create(User $user): bool
    {
        return $user->can('create user');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->can('edit user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->can('delete user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function restore(User $user, User $model): bool
    {
        if ($user->can('restore user')) {
            return true;
        }

        return $user->id === $model->id;
    }

    public function forceDelete(User $user, User $model): bool
    {
        if ($user->can('delete user')) {
            return true;
        }

        return $user->id === $model->id;
    }
}
