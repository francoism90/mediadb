<?php

namespace App\Console\Commands\Media;

use App\Jobs\Media\CreatePreview;
use App\Jobs\Media\CreateSprite;
use App\Jobs\Media\CreateThumbnail;
use App\Jobs\Media\SetAttributes;
use App\Jobs\Media\SetProcessed;
use App\Models\Media;
use Illuminate\Console\Command;
use Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection;

class Optimize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'media:optimize {--force}';

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
        $models = $this->getInvalidMediaModels();

        if ($this->option('force')) {
            $models = $this->getRandomMediaModels();
        }

        foreach ($models as $model) {
            if ($this->option('force')) {
                $this->warn("Force optimizing: {$model->id}");

                SetAttributes::withChain([
                    new CreateThumbnail($model),
                    new CreatePreview($model),
                    new CreateSprite($model),
                    new SetProcessed($model),
                ])->dispatch($model)->allOnQueue('optimize');
                continue;
            }

            $this->hasMissingAttributes($model)
                 ->hasMissingConversions($model);
        }
    }

    /**
     * @return MediaCollection
     */
    protected function getInvalidMediaModels(): MediaCollection
    {
        $query = Media::WhereNull('custom_properties->duration')
            ->orWhereNull('custom_properties->generated_conversions')
            ->orWhereNull('custom_properties->generated_conversions->preview')
            ->orWhereNull('custom_properties->generated_conversions->sprite')
            ->orWhereNull('custom_properties->generated_conversions->thumbnail')
            ->inRandomOrder()
            ->take(config('vod.optimize_limit', 5))
            ->get();

        return $query;
    }

    /**
     * @return MediaCollection
     */
    protected function getRandomMediaModels(): MediaCollection
    {
        $query = Media::inRandomOrder()
                ->take(config('vod.optimize_limit', 5))
                ->get();

        return $query;
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
        ])->dispatch($media)->allOnQueue('optimize');

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

            CreateThumbnail::dispatch($media)->onQueue('optimize');
        }

        if (!$media->hasGeneratedConversion('preview')) {
            $this->warn("Missing preview: {$media->id}");

            CreatePreview::dispatch($media)->onQueue('optimize');
        }

        if (!$media->hasGeneratedConversion('sprite')) {
            $this->warn("Missing sprite: {$media->id}");

            CreateSprite::dispatch($media)->onQueue('optimize');
        }

        return $this;
    }
}
