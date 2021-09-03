<?php

namespace App\Services\Streamers;

use App\Models\Video;

interface StreamerInterface
{
    public function getUrl(string $location, string $uri): string;
}
