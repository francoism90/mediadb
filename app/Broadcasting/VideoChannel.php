<?php

namespace App\Broadcasting;

use App\Models\User;
use App\Models\Video;

class VideoChannel
{
    public function join(User $user, Video $video): bool
    {
        return true;
    }
}
