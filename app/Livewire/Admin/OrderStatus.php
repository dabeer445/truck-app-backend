<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Order;

class OrderStatus extends Component
{
    public Order $order;
    public $currentStatus;
    
    public function mount(Order $order)
    {
        $this->order = $order;
        $this->currentStatus = $order->status;
    }

    public function updateStatus($status)
    {
        $this->order->status = $status;
        $this->order->save();
        
        $this->currentStatus = $status;
        
        $this->dispatch('showNotification', [
            'type' => 'success',
            'message' => 'Order status updated successfully'
        ]);
    }

    public function render()
    {
        return view('livewire.admin.order-status', [
            'statusColors' => [
                'pending' => 'bg-yellow-100 text-yellow-800',
                'in_progress' => 'bg-blue-100 text-blue-800',
                'completed' => 'bg-green-100 text-green-800',
                'cancelled' => 'bg-red-100 text-red-800',
            ],
            'statuses' => [
                'pending' => 'Pending',
                'in_progress' => 'In Progress',
                'completed' => 'Completed',
                'cancelled' => 'Cancelled'
            ]
        ]);
    }
}