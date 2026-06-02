<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Show the customer's order history.
     */
    public function index()
    {
        $orders = Order::where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show order detail for a customer.
     */
    public function show(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.orders.detail', compact('order'));
    }
}
