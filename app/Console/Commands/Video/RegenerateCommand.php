<?php

namespace App\Console\Commands\Video;

use App\Actions\Video\BulkRegenerateVideos;
use Illuminate\Console\Command;

class RegenerateCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:regenerate';

    /**
     * @var string
     */
    protected $description = 'Regenerate video models';

    public function handle(): void
    {
        app(BulkRegenerateVideos::class)->execute();
    }
}
