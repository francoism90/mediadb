<?php

namespace App\Events\Tag;

use App\Models\Tag;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HasBeenDeleted implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public Tag $tag
    ) {
    }

    public function broadcastAs(): string
    {
        return 'tag.deleted';
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'tag.'.$this->tag->getRouteKey()
        );
    }
}
