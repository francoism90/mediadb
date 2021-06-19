<?php

namespace App\Policies;

use App\Models\Media;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return false;
    }

    public function view(?User $user, Media $model): bool
    {
        if (null === $user) {
            return false;
        }

        return true;
    }

    public function create(User $user): bool
    {
        if ($user->can('create media')) {
            return true;
        }

        return false;
    }

    public function update(User $user, Media $model): bool
    {
        if ($user->can('edit media')) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Media $model): bool
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }

    public function restore(User $user, Media $model): bool
    {
        if ($user->can('restore media')) {
            return true;
        }

        return false;
    }

    public function forceDelete(User $user, Media $model): bool
    {
        if ($user->can('delete media')) {
            return true;
        }

        return false;
    }
}
