<?php

namespace App\Console\Commands\Video;

use App\Actions\Video\BulkImportVideos;
use App\Models\User;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import
        {path : Import path to use}
        {user=1 : Import to user}';

    /**
     * @var string
     */
    protected $description = 'Import videos to a user';

    public function handle(
        BulkImportVideos $bulkImportVideos,
    ): void {
        $this->info('Starting videos import..');

        $bulkImportVideos->execute(
            $this->getUser(),
            $this->argument('path'),
        );
    }

    protected function getUser(): User
    {
        return User::findOrFail($this->argument('user'));
    }
}
