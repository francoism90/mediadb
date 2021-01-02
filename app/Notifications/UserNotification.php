<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class UserNotification extends Notification
{
    /**
     * @var mixed
     */
    public $message;

    /**
     * @param mixed $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return ['broadcast', 'database'];
    }

    /**
     * @param mixed $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage(
            $this->getMessage()
        );
    }

    /**
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toDatabase($notifiable): array
    {
        return $this->getMessage();
    }

    /**
     * @return string
     */
    public function broadcastType(): string
    {
        return $this->getMessage()['type'] ?? 'info';
    }

    /**
     * @return mixed
     */
    protected function getMessage()
    {
        if ($this->message instanceof Collection) {
            return $this->message->toArray();
        }

        return $this->message;
    }
}
