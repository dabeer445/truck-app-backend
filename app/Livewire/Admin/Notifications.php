<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Notifications\NewOrderNotification;

class Notifications extends Component
{
    use WithPagination;

    // function __construct()
    // {
    //     request()->user()->notify(new NewOrderNotification(Order::latest()->first()));
    //     dd(request()->user()
    //         ->notifications()->latest()
    //         ->paginate(10));
    //     $admin = request()->user();
    //     $order = Order::latest()->first();
    //     $admin->notify(new NewOrderNotification($order));

    // }

    public function markAsRead($notificationId)
    {
        request()->user()
            ->notifications()
            ->findOrFail($notificationId)
            ->markAsRead();
    }

    public function markAllAsRead()
    {
        request()->user()
            ->unreadNotifications()
            ->update(['read_at' => now()]);
    }

    public function render()
    {
        $notifications = request()->user()
            ->notifications()
            ->latest()
            ->paginate(6);

        return view('livewire.admin.notifications', compact('notifications'));
    }
}
