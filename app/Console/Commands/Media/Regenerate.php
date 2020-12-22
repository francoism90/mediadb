<?php

namespace App\Console\Commands\Media;

use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
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
    protected $description = 'Regenerate missing conversions on media models';

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
        $models = Media::missingConversions()->cursor();

        foreach ($models as $model) {
            if (!$model->hasGeneratedConversion('thumbnail')) {
                CreateThumbnail::dispatch($model)->onQueue('media');
            }

            if (!$model->hasGeneratedConversion('sprite')) {
                CreateSprite::dispatch($model)->onQueue('media');
            }
        }
    }
}
