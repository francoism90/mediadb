<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    public function viewAny(?User $user): bool
    {
        return null !== $user;
    }

    public function view(?User $user, Video $model): bool
    {
        if (null === $user) {
            return false;
        }

        if ($model->latestStatus('private')->exists()) {
            return false;
        }

        return $user->id === $model->model->id;
    }

    public function create(User $user): bool
    {
        return $user->can('create video');
    }

    public function update(User $user, Video $model): bool
    {
        if ($user->can('edit video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    public function delete(User $user, Video $model): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    public function restore(User $user, Video $model): bool
    {
        if ($user->can('restore video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }

    public function forceDelete(User $user, Video $model): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return $user->id === $model->model->id;
    }
}
