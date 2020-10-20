<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BroadcastNotification extends Notification
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
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    /**
     * @param mixed $notifiable
     *
     * @return mixed
     */
    public function toBroadcast($notifiable)
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
        if ($this->message instanceof \Illuminate\Support\Collection) {
            return $this->message->toArray();
        }

        return $this->message;
    }
}
