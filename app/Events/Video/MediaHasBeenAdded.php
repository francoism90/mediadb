<?php

namespace App\Events\Video;

use App\Models\Media;
use App\Models\Video;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaHasBeenAdded
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Video
     */
    public $video;

    /**
     * @var Media
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Video $video, Media $media)
    {
        $this->video = $video;
        $this->media = $media;
    }
}
