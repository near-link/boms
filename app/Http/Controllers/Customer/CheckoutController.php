<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    /**
     * Show the checkout page.
     */
    public function index()
    {
        $cartItems = CartItem::with('product.vendor')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->qty * $item->product->price);
        $deliveryFee = 2.00;
        $total = $subtotal + $deliveryFee;
        $user = Auth::user();

        return view('customer.checkout', compact('cartItems', 'subtotal', 'deliveryFee', 'total', 'user'));
    }

    /**
     * Process the checkout and create orders.
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_location' => 'required|string',
            'delivery_date' => 'required|date',
            'time_slot' => 'required|string',
            'payment_method' => 'required|in:cash,online',
        ]);

        $cartItems = CartItem::with('product.vendor')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index');
        }

        // Group items by vendor
        $grouped = $cartItems->groupBy(fn($item) => $item->product->vendor->name);

        $lastOrder = null;
        foreach ($grouped as $vendorName => $items) {
            $orderItems = $items->map(fn($item) => [
                'name' => $item->product->name,
                'qty' => $item->qty,
                'price' => (float) $item->product->price,
            ])->values()->toArray();

            $subtotal = $items->sum(fn($item) => $item->qty * $item->product->price);
            $deliveryFee = 2.00;
            $total = $subtotal + $deliveryFee;

            $locationNames = [
                'blkA' => 'Block A - Main Lobby',
                'blkB' => 'Block B - Cafeteria',
                'lib' => 'Library Entrance',
                'dewan' => 'Dewan Kuliah Utama',
            ];

            $lastOrder = Order::create([
                'order_code' => Order::generateOrderCode(),
                'customer_id' => Auth::id(),
                'vendor_name' => $vendorName,
                'delivery_location' => $locationNames[$request->delivery_location] ?? $request->delivery_location,
                'delivery_date' => $request->delivery_date,
                'time_slot' => $request->time_slot,
                'items' => $orderItems,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'paid',
                'notes' => $request->notes,
                'status' => 'pending',
            ]);
        }

        // Clear cart
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('checkout.confirmation', $lastOrder->id)
            ->with('success', 'Order placed successfully!');
    }

    /**
     * Show the order confirmation page.
     */
    public function confirmation(Order $order)
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        return view('customer.confirmation', compact('order'));
    }
}
