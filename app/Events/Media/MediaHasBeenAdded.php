<?php

namespace App\Events\Media;

use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MediaHasBeenAdded implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Model $model,
        public Media $media
    ) {
    }

    public function broadcastAs(): string
    {
        return 'media.added';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'media.'.$this->media->getRouteKey()
        );
    }
}
