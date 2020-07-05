<?php

namespace App\Console\Commands\Media;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\SetAttributes;
use App\Jobs\Media\SetProcessed;
use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class Optimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize {validate=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize media models';

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
        $models = Media::all();

        foreach ($models as $model) {
            if ($this->argument('validate') && $this->hasMissingMainFile($model)) {
                $this->error("Missing file: {$model->id}");
                continue;
            }

            $this->hasMissingAttributes($model)
                 ->hasMissingConversions($model);
        }
    }

    /**
     * @return bool
     */
    protected function hasMissingMainFile(Media $media): bool
    {
        return !file_exists($media->getPath());
    }

    /**
     * @param Media $media
     *
     * @return self
     */
    protected function hasMissingAttributes(Media $media): self
    {
        if ($this->hasValidAttributes($media) && $media->hasEverHadStatus('processed')) {
            return $this;
        }

        $this->warn("Missing attributes: {$media->id}");

        SetAttributes::withChain([
            new SetProcessed($media),
        ])->dispatch($media)->allOnQueue('media');

        return $this;
    }

    /**
     * @param Media $media
     *
     * @return bool
     */
    protected function hasValidAttributes(Media $media): bool
    {
        return
            $media->hasCustomProperty('duration') &&
            $media->hasCustomProperty('width') &&
            $media->hasCustomProperty('height')
        ;
    }

    /**
     * @param Media $media
     *
     * @return self
     */
    protected function hasMissingConversions(Media $media): self
    {
        if (!$media->hasGeneratedConversion('thumbnail')) {
            $this->warn("Missing thumbnail: {$media->id}");

            Artisan::call('media-library:regenerate', [
                '--ids' => $media->id,
                '--force' => true,
            ]);
        }

        if (!$media->hasGeneratedConversion('preview')) {
            $this->warn("Missing preview: {$media->id}");

            CreatePreview::dispatch($media);
        }

        return $this;
    }
}
