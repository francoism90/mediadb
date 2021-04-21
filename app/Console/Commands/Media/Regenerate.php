<?php

namespace App\Console\Commands\Media;

use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\SetMetadata;
use App\Models\Media;
use Illuminate\Console\Command;

class Regenerate extends Command
{
    /**
     * @var string
     */
    protected $signature = 'media:regenerate';

    /**
     * @var string
     */
    protected $description = 'Regenerate media models';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->dispatchMissingMetadata();
        $this->dispatchMissingConversions();
    }

    /**
     * @return void
     */
    protected function dispatchMissingMetadata(): void
    {
        $models = Media::missingMetadata()->cursor();

        foreach ($models as $model) {
            SetMetadata::dispatch($model)->onQueue('media');
        }
    }

    /**
     * @return void
     */
    protected function dispatchMissingConversions(): void
    {
        $models = Media::missingConversions()->cursor();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('media');
            }
        }
    }
}
