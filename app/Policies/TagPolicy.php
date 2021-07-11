<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TagPolicy
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
        return $user->can('create tag');
    }

    public function update(User $user): bool
    {
        return $user->can('edit tag');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete tag');
    }

    public function restore(User $user): bool
    {
        return $user->can('restore tag');
    }

    public function forceDelete(User $user): bool
    {
        return $user->can('delete tag');
    }
}
