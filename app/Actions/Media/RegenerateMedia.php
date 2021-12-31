<?php

namespace App\Actions\Media;

use App\Jobs\Media\Optimize;
use App\Jobs\Media\Process;
use App\Jobs\Media\Release;
use App\Models\Media;
use Illuminate\Support\Facades\Bus;

class RegenerateMedia
{
    public function __invoke(Media $media): void
    {
        Bus::chain([
            new Process($media),
            new Optimize($media),
            new Release($media),
        ])->onQueue('processing')->dispatch();
    }
}
