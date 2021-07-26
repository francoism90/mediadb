<?php

namespace App\Console\Commands\Video;

use App\Models\User;
use App\Services\VideoService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Helper\ProgressBar;

class ImportCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import
        {path? : Import path to use}
        {user=1 : Import to user}';

    /**
     * @var string
     */
    protected $description = 'Import videos to a user';

    protected Collection $results;

    protected ?ProgressBar $progressBar = null;

    public function handle(
        VideoService $videoService
    ): void {
        $this->info('Starting videos import..');

        $user = $this->getUser();

        $videoService->import(
            $user,
            $this->argument('path'),
            $this
        );
    }

    public function logStatusToConsole(Collection $results): void
    {
        $this->results = $results;

        $this->table(
            ['Name', 'Success'],
            $this->results->toArray()
        );
    }

    public function createProgressBar(int $max): void
    {
        $this->progressBar = $this->getOutput()->createProgressBar($max);
    }

    public function advanceProgressBar(): void
    {
        $this->progressBar->advance();
    }

    protected function getUser(): User
    {
        return User::findOrFail($this->argument('user'));
    }
}
