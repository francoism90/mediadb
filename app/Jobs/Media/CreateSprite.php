<?php

namespace App\Jobs\Media;

use App\Models\Media;
use App\Services\MediaSpriteService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateSprite implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * @var bool
     */
    public $deleteWhenMissingModels = true;

    /**
     * @var int
     */
    public $tries = 1;

    /**
     * @var int
     */
    public $timeout = 3600;

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
    public function handle(MediaSpriteService $mediaSpriteService)
    {
        $mediaSpriteService->create($this->media);
    }

    /**
     * @return array
     */
    public function tags(): array
    {
        return ['sprite', 'media:'.$this->media->id];
    }
}
