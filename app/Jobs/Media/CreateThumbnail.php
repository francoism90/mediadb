<?php

namespace App\Jobs\Media;

use App\Models\Media;
use App\Services\ThumbnailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateThumbnail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public $dispatchAfterCommit = true;

    /**
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var int
     */
    public $tries = 3;

    /**
     * @var int
     */
    public $timeout = 300;

    /**
     * @var Media
     */
    protected $media;

    /**
     * @return void
     */
    public function __construct(Media $media)
    {
        $this->media = $media->withoutRelations();
    }

    /**
     * @return void
     */
    public function handle(ThumbnailService $thumbnailService)
    {
        $thumbnailService->create($this->media);
    }

    /**
     * @return array
     */
    public function tags()
    {
        return ['thumbnail', 'media:'.$this->media->id];
    }
}
