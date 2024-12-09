<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function show(Order $order)
    {
        // Load relationships if needed
        $order->load(['user']);

        return view('admin.orders.show', compact('order'));
    }
}