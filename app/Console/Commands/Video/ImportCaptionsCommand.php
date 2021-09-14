<?php

namespace App\Console\Commands\Video;

use App\Actions\Video\BulkImportCaptions;
use App\Models\User;
use App\Models\Video;
use Illuminate\Console\Command;

class ImportCaptionsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import
        {path : Import path to use}
        {video : Import to video}';

    /**
     * @var string
     */
    protected $description = 'Import captions to a video';

    public function handle(
        BulkImportCaptions $bulkImportCaptions,
    ): void {
        $bulkImportCaptions(
            $this->getVideo(),
            $this->argument('path'),
        );
    }

    protected function getVideo(): User
    {
        return Video::findOrFail($this->argument('video'));
    }
}
