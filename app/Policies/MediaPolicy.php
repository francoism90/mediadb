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
        return null !== $user;
    }

    public function create(User $user): bool
    {
        return $user->can('create media');
    }

    public function update(User $user, Media $model): bool
    {
        return $user->can('edit media');
    }

    public function delete(User $user, Media $model): bool
    {
        return $user->can('delete media');
    }

    public function restore(User $user, Media $model): bool
    {
        return $user->can('restore media');
    }

    public function forceDelete(User $user, Media $model): bool
    {
        return $user->can('delete media');
    }
}
