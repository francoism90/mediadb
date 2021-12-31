<?php

namespace App\Actions\Video;

use App\Jobs\Video\Optimize;
use App\Jobs\Video\Process;
use App\Jobs\Video\Release;
use App\Models\Video;
use Illuminate\Support\Facades\Bus;

class RegenerateVideo
{
    public function __invoke(Video $video): void
    {
        Bus::chain([
            new Process($video),
            new Optimize($video),
            new Release($video),
        ])->onQueue('processing')->dispatch();
    }
}
