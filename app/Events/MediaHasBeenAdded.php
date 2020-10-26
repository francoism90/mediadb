<?php

namespace App\Events;

use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaHasBeenAdded
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var Media
     */
    public $media;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Model $model, Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }
}
