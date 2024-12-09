<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Message;
use App\Notifications\NewMessageNotification;
use App\Models\Order;
use Livewire\Attributes\On;

class OrderMessages extends Component
{
    public Order $order;
    public $newMessage = '';
    
    public function mount(Order $order)
    {
        $this->order = $order;
        $this->markMessagesAsRead();
    }

    // Mark messages as read when viewing
    private function markMessagesAsRead()
    {
        Message::where('order_id', $this->order->id)
            ->where('receiver_id', auth('sanctum')->id())
            ->where('is_read', false)
            ->each(function ($message) {
                $message->markAsRead();
            });
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|min:1|max:1000'
        ]);

        $message = Message::create([
            'order_id' => $this->order->id,
            'sender_id' => auth('sanctum')->id(),
            'receiver_id' => $this->order->user_id,
            'content' => $this->newMessage,
            'is_read' => false,
            'read_at' => null
        ]);

        $this->newMessage = '';
        // Notify the receiver
        $message->receiver->notify(new NewMessageNotification($message));

        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'Message sent successfully'
        ]);
    }

    #[On('refresh-messages')]
    public function refreshMessages()
    {
        $this->markMessagesAsRead();
    }

    public function getMessagesProperty()
    {
        return Message::where('order_id', $this->order->id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.order-messages', [
            'messages' => $this->messages
        ]);
    }
}