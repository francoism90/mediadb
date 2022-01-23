<?php

namespace App\Events\User;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserHasBeenUpdated implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public User $user
    ) {
    }

    public function broadcastAs(): string
    {
        return 'user.updated';
    }

    public function broadcastWith(): array
    {
        return (new UserResource($this->user))->resolve();
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'user.'.$this->user->getRouteKey()
        );
    }
}
