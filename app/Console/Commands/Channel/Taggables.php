<?php

namespace App\Console\Commands\Channel;

use App\Models\Channel;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class Taggables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'channel:taggables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Attach model tags to child models';

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
        $models = $this->getModels();

        foreach ($models as $model) {
            $this->attachTagsToMedia($model);
        }
    }

    /**
     * @return Collection
     */
    protected function getModels(): Collection
    {
        return Channel::has('tags')
            ->with(['media', 'tags'])
            ->get();
    }

    /**
     * @param Channel $channel
     *
     * @return self
     */
    protected function attachTagsToMedia(Channel $channel): self
    {
        // Get channel tags
        $tags = $channel->tags->all();

        // Attach channel tags to each media model
        $media = $channel->media;

        foreach ($media as $model) {
            $model->attachTags($tags);
        }

        return $this;
    }
}
