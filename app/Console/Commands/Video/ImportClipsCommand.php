<?php

namespace App\Console\Commands\Video;

use App\Actions\Video\BulkImportClips;
use App\Models\User;
use App\Models\Video;
use Illuminate\Console\Command;

class ImportClipsCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import-clips
        {path : Import path to use}
        {video : Import to video}';

    /**
     * @var string
     */
    protected $description = 'Import clips to a video';

    public function handle(
        BulkImportClips $bulkImportClips,
    ): void {
        $bulkImportClips(
            $this->getVideo(),
            $this->argument('path'),
        );
    }

    protected function getVideo(): User
    {
        return Video::findOrFail($this->argument('video'));
    }
}
