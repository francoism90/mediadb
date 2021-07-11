<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MediaPolicy
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
        return $user->can('create media');
    }

    public function update(User $user): bool
    {
        return $user->can('edit media');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete media');
    }

    public function restore(User $user): bool
    {
        return $user->can('restore media');
    }

    public function forceDelete(User $user): bool
    {
        return $user->can('delete media');
    }
}
