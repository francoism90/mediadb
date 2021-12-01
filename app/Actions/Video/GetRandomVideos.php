<?php

namespace App\Actions\Video;

use App\Models\Video;
use Illuminate\Database\Eloquent\Builder;

class GetRandomVideos
{
    public function __invoke(): Builder
    {
        return Video::inRandomOrder();
    }
}
