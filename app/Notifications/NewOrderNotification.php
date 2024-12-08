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

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'type' => 'new_order',
            'message' => "New order #{$this->order->id} created",
            'data' => [
                'pickup_location' => $this->order->pickup_location,
                'delivery_location' => $this->order->delivery_location,
                'status' => $this->order->status,
            ]
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
