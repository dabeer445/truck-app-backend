<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\Attributes\Computed;

class Dashboard extends Component
{
    #[Computed]
    public function stats()
    {
        return [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'in_progress_orders' => Order::where('status', 'in_progress')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'cancelled_orders' => Order::where('status', 'cancelled')->count(),
        ];
    }
    
    #[Computed]
    public function recentOrders()
    {
        return Order::with('user')
            ->latest()
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
