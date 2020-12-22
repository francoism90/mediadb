<?php

namespace App\Console\Commands\Media;

use App\Services\MediaStreamService;
use Illuminate\Console\Command;

class Clean extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:clean';

    /**
     * @var string
     */
    protected $description = 'Delete temporary files from media models';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle(MediaStreamService $mediaStreamService): void
    {
        $files = $mediaStreamService->getExpiredMappingFiles();

        foreach ($files as $file) {
            $path = $file->getRealPath();

            if (!unlink($path)) {
                $this->error("Unable to delete {$path}");
            }
        }
    }
}
