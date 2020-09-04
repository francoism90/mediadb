<?php

namespace App\Console\Commands\Video;

use App\Models\Collection;
use App\Models\User;
use App\Services\Video\ImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection as IlluminateCollection;

class Import extends Command
{
    /**
     * @var string
     */
    protected $signature = 'video:import {user} {path} {type=episode}';

    /**
     * @var string
     */
    protected $description = 'Import video files to library';

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
    public function handle(ImportService $importService)
    {
        $user = $this->getUserModel();
        $collection = $this->getCollectionName();

        $importService->import(
            $user,
            $collection,
            $this->argument('path'),
            $this->argument('type')
        );
    }

    /**
     * @return string
     */
    protected function getCollectionName(): string
    {
        $name = $this->anticipate('Add videos to collection', function ($input) {
            return $this->getCollectionsByQuery($input)->pluck('name')->toArray();
        });

        return $name;
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

    /**
     * @return array
     */
    protected function getCollectionsByQuery(string $query = ''): IlluminateCollection
    {
        return Collection::search($query)
            ->select(['id', 'name'])
            ->from(0)
            ->take(5)
            ->get();
    }
}
