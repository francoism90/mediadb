<?php

namespace App\Events\Video;

use App\Models\Video;
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
        public Video $video
    ) {
    }

    public function broadcastAs(): string
    {
        return 'video.updated';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'video.'.$this->video->getRouteKey()
        );
    }
}
