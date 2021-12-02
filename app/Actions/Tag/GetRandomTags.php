<?php

namespace App\Actions\Tag;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Builder;

class GetRandomTags
{
    public function __invoke(): Builder
    {
        return Tag::inRandomOrder();
    }
}
