<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class NewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;
    public $order;
    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database', 'mail']; // Can add more channels
    }
    protected function getTitle(): string
    {
        return "New Order #{$this->order->id}";
    }

    protected function getMessage(): string
    {
        return "New order created";
    }

    protected function getOrderId(): int
    {
        return $this->order->id;
    }

    protected function getSenderId(): ?int
    {
        return $this->order->user_id;
    }

    protected function getExtraData(): array
    {
        return [
            'pickup_location' => $this->order->pickup_location,
            'delivery_location' => $this->order->delivery_location,
            'status' => $this->order->status,

        ];
    }
    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'new_order',
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
            'order_id' => $this->getOrderId(),
            'sender_id' => $this->getSenderId(),
            'extra_data' => $this->getExtraData(),
        ];
    }
    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("New Order #{$this->order->id}")
            ->line("A new order has been created.")
            ->action('View Order', url("/orders/{$this->order->id}"))
            ->line('Thank you for using our application!');
    }
}
