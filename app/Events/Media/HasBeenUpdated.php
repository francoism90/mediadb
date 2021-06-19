<?php

namespace App\Events\Media;

use App\Models\Media;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HasBeenUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Media $media
    ) {
    }

    public function broadcastAs(): string
    {
        return 'media.updated';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'media.'.$this->media->getRouteKey()
        );
    }
}
