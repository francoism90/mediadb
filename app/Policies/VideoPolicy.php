<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Video;
use Illuminate\Auth\Access\HandlesAuthorization;

class VideoPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return null !== $user;
    }

    public function view(User $user, Video $video): bool
    {
        if (null === $user) {
            return false;
        }

        return 'private' !== $video->getAttribute('status');
    }

    public function create(User $user): bool
    {
        return $user->can('create video');
    }

    public function update(User $user, Video $video): bool
    {
        if ($user->can('edit video')) {
            return true;
        }

        return
            $video->model_type === get_class($user) &&
            $video->model_id === $user->id
        ;
    }

    public function delete(User $user, Video $video): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return
            $video->model_type === get_class($user) &&
            $video->model_id === $user->id
        ;
    }

    public function restore(User $user, Video $video): bool
    {
        if ($user->can('restore video')) {
            return true;
        }

        return
            $video->model_type === get_class($user) &&
            $video->model_id === $user->id
        ;
    }

    public function forceDelete(User $user, Video $video): bool
    {
        if ($user->can('delete video')) {
            return true;
        }

        return
            $video->model_type === get_class($user) &&
            $video->model_id === $user->id
        ;
    }
}
