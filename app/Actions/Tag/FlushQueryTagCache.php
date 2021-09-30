<?php

namespace App\Actions\Tag;

use App\Models\Tag;

class FlushQueryTagCache
{
    public function __invoke(): void
    {
        Tag::flushQueryCache();
    }
}
