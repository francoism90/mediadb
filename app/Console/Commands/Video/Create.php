<?php

namespace App\Console\Commands\Video;

use App\Models\User;
use Illuminate\Console\Command;

class Create extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:create {name} {user=1}';

    /**
     * @var string
     */
    protected $description = 'Create a video model';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return mixed
     */
    public function handle()
    {
        $user = $this->getUserModel();
        $type = $this->getType();

        // Create video model
        $video = $user->videos()->create([
            'name' => $this->argument('name'),
            'type' => $type,
        ]);

        $this->info("Successfully created model ({$video->id})");
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return $this->choice(
            'Choose type',
            config('vod.import.types'),
        );
    }

    /**
     * @return User
     */
    protected function getUserModel(): User
    {
        return User::findOrFail(
            $this->argument('user')
        );
    }
}
