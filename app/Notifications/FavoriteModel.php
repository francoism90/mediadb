<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class FavoriteModel extends Notification
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
            'favorite' => $this->model->favorite,
        ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'id' => $this->model->id,
            'favorite' => $this->model->favorite,
        ];
    }
}
