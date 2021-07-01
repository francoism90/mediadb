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
        return null !== $user;
    }

    public function view(?User $user, Tag $model): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->can('create tag');
    }

    public function update(User $user, Tag $model): bool
    {
        return $user->can('edit tag');
    }

    public function delete(User $user, Tag $model): bool
    {
        return $user->can('delete tag');
    }

    public function restore(User $user, Tag $model): bool
    {
        return $user->can('restore tag');
    }

    public function forceDelete(User $user, Tag $model): bool
    {
        return $user->can('delete tag');
    }
}
