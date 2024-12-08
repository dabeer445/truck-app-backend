<?php

namespace App\Services;

use App\Models\Order;
use App\Events\OrderCreated;
use App\Notifications\OrderStatusUpdated;

class OrderService
{
    public function createOrder(array $validated)
    {
        $order = Order::create($validated);
        // event(new OrderCreated($order));
        return $order;
    }

    public function updateStatus(Order $order, string $status)
    {
        $order->update(['status' => $status]);
        // $order->user->notify(new OrderStatusUpdated($order));
        return $order;
    }

    public function getUserOrders($userId)
    {
        return Order::where('user_id', $userId)
            ->latest()
            ->paginate(10);
    }
}