<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function orders()
    {
        return view('admin.orders.index');
    }

    public function showOrder(Order $order)
    {
        return view('admin.orders.show', compact('order'));
    }

    public function chat()
    {
        return view('admin.chat.index');
    }

    public function notifications()
    {
        return view('admin.notifications.index');
    }
}