<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ModelHasBeenFollowed implements ShouldBroadcastNow
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public User $user,
        public Model $model
    ) {
    }

    public function broadcastAs(): string
    {
        return 'model.followed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->model->getRouteKey(),
            'following' => $this->model->following,
        ];
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel(
            'user.'.$this->user->getRouteKey()
        );
    }
}
