<?php

namespace App\Livewire\Admin;

use App\Models\Message;
use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Chat extends Component
{
    use WithPagination;
    
    public $selectedOrder = null;
    public $message = '';
    public $searchOrder = '';
    public $orders = [];
    
    public function mount()
    {
        $this->loadOrders();
    }
    
    public function loadOrders()
    {
        $this->orders = Order::with(['messages' => function($q) {
            $q->latest();
        }, 'user'])
        ->whereHas('messages', function($q) {
            $q->where('sender_id', auth('sanctum')->id())
                ->orWhere('receiver_id', auth('sanctum')->id());
        })
        ->when($this->searchOrder, function($q) {
            $q->where('id', 'like', "%{$this->searchOrder}%");
        })
        ->latest()
        ->get();
    }
    
    public function selectOrder($orderId)
    {
        $this->selectedOrder = Order::with(['messages.sender', 'messages.receiver', 'user'])->find($orderId);
        $this->markMessagesAsRead();
    }
    
    public function markMessagesAsRead()
    {
        if ($this->selectedOrder) {
            $unreadMessages = Message::where('order_id', $this->selectedOrder->id)
                ->where('receiver_id', auth('sanctum')->id())
                ->where('is_read', false)
                ->get();
                
            foreach($unreadMessages as $message) {
                $message->markAsRead();
            }
        }
    }
    
    public function sendMessage()
    {
        if (!$this->selectedOrder || !trim($this->message)) {
            return;
        }
        
        $message = Message::create([
            'order_id' => $this->selectedOrder->id,
            'sender_id' => auth('sanctum')->id(),
            'receiver_id' => $this->selectedOrder->user_id,
            'content' => $this->message,
            'is_read' => false
        ]);
        
        $this->message = '';
        $this->selectedOrder->refresh();
    }

    public function render()
    {
        return view('livewire.admin.chat');
    }
}