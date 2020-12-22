<?php

namespace App\Console\Commands\Media;

use App\Jobs\Media\SetMetadata;
use App\Models\Media;
use Illuminate\Console\Command;

class Maintenance extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:maintenance';

    /**
     * @var string
     */
    protected $description = 'Perform maintenance on media models';

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
    public function handle(): void
    {
        $models = Media::missingMetadata()->cursor();

        foreach ($models as $model) {
            SetMetadata::dispatch($model)->onQueue('media');
        }
    }
}
