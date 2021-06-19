<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return false;
    }

    public function view(?User $user, User $model): bool
    {
        if (null === $user) {
            return false;
        }

        return $user->id === $model->id;
    }

    public function create(User $user): bool
    {
        if ($user->can('create user')) {
            return true;
        }

        return false;
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
