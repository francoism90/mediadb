<?php

namespace App\Jobs\Media;

use App\Models\Media;
use App\Services\SpriteService;
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
    public $dispatchAfterCommit = true;

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
    public $timeout = 5400;

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
    public function handle(SpriteService $spriteService)
    {
        $spriteService->create($this->media);
    }

    /**
     * @return array
     */
    public function tags()
    {
        return ['sprite', 'media:'.$this->media->id];
    }
}
