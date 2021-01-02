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

    /**
     * @var string
     */
    public string $broadcastQueue = 'broadcasts';

    /**
     * @var Tag
     */
    public Tag $tag;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Tag $tag)
    {
        $this->tag = $tag;
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'tag.deleted';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'tag.'.$this->tag->getRouteKey()
        );
    }
}
