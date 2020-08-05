<?php

namespace App\Console\Commands\Channel;

use App\Models\Channel;
use App\Models\User;
use App\Services\MediaUploadService;
use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * @var string
     */
    protected $signature = 'channel:import {path} {channel=Administrator} {user=administrator} {collection=videos}';

    /**
     * @var string
     */
    protected $description = 'Import media file(s) by path';

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
    public function handle(MediaUploadService $mediaUploadService)
    {
        $channel = $this->findOrCreateChannel();

        $mediaUploadService->importByPath(
            $channel,
            $this->argument('path'),
            $this->argument('collection')
        );
    }

    /**
     * Create the channel if not exists.
     * Media will be added to the channel.
     *
     * @return Channel
     */
    protected function findOrCreateChannel(): Channel
    {
        $user = User::findBySlugOrFail($this->argument('user'));

        $channel = $user->channels()->firstOrCreate(
            ['name' => $this->argument('channel')]
        );

        if (!$channel->status()) {
            $channel->setStatus('published');
        }

        return $channel;
    }
}
