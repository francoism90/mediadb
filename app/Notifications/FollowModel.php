<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FollowModel extends Notification
{
    use Queueable;

    public function __construct(public Model $model)
    {
    }

    public function via($notifiable): array
    {
        return ['broadcast', 'database'];
    }

    public function broadcastType(): string
    {
        return 'broadcast.message';
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'id' => $this->model->id,
            'follow' => $this->model->follow,
        ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id' => $this->model->id,
            'follow' => $this->model->follow,
        ];
    }
}
