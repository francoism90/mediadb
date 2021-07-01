<?php

namespace App\Broadcasting;

use App\Models\Media;
use App\Models\User;

class MediaChannel
{
    public function join(User $user, Media $model): bool
    {
        return true;
    }
}
