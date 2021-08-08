<?php

namespace App\Broadcasting;

use App\Models\Tag;
use App\Models\User;

class TagChannel
{
    public function join(User $user, Tag $tag): bool
    {
        return true;
    }
}
