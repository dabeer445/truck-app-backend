<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use App\Models\Message;

class NewMessageNotification extends Notification implements ShouldQueue
{
    private $message;
    use Queueable;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message_id' => $this->message->id,
            'order_id' => $this->message->order_id,
            'sender_id' => $this->message->sender_id,
            'content' => Str::limit($this->message->content, 100),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New message regarding Order #{$this->message->order_id}")
            ->line("You have received a new message from {$this->message->sender->name}")
            ->action('View Message', url("/orders/{$this->message->order_id}/messages"))
            ->line($this->message->content);
    }
}
