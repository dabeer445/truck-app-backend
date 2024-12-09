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
    protected function getTitle(): string
    {
        return "New Message";
    }
    
    protected function getMessage(): string
    {
        return Str::limit($this->message->content, 100);
    }
    
    protected function getOrderId(): int
    {
        return $this->message->order_id;
    }
    
    protected function getSenderId(): int
    {
        return $this->message->sender_id;
    }
    
    protected function getExtraData(): array
    {
        return [
            'message_id' => $this->message->id
        ];
    }
    public function toDatabase($notifiable)
    {
        return [
            'type' => 'new_message', 
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'order_id' => $this->getOrderId(),
            'sender_id' => $this->getSenderId(),
            'extra_data' => $this->getExtraData(),
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
