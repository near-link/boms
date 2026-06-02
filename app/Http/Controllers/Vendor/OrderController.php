<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Get order detail as JSON (for slide-in panel).
     */
    public function show(Order $order)
    {
        $order->load('customer');
        return response()->json([
            'id' => $order->id,
            'code' => $order->order_code,
            'customer' => $order->customer_display_name,
            'items' => $order->items,
            'subtotal' => (float) $order->subtotal,
            'deliveryFee' => (float) $order->delivery_fee,
            'total' => (float) $order->total,
            'status' => $order->status,
            'location' => $order->delivery_location,
            'timeSlot' => $order->time_slot,
            'deliveryDate' => $order->delivery_date->format('d M Y'),
            'notes' => $order->notes,
            'vendorNote' => $order->vendor_note,
            'createdAt' => $order->created_at->format('d M Y, g:i A'),
        ]);
    }

    /**
     * Show the vendor manual order form.
     */
    public function create()
    {
        return view('vendor.orders.create');
    }

    /**
     * Store a vendor manual order (walk-in).
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'delivery_location' => 'required|string',
            'time_slot' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $items = $request->items;
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['qty'] * $item['price'];
        }
        $deliveryFee = 2.00;
        $total = $subtotal + $deliveryFee;

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'customer_id' => null,
            'customer_name' => $request->customer_name,
            'vendor_name' => Auth::user()->name,
            'delivery_location' => $request->delivery_location,
            'delivery_date' => now()->toDateString(),
            'time_slot' => $request->time_slot,
            'items' => $items,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('vendor.dashboard')
            ->with('success', "Order {$order->order_code} created.");
    }

    /**
     * Update an order's status and/or vendor note.
     */
    public function update(Request $request, Order $order)
    {
        $data = [];

        if ($request->has('status')) {
            $request->validate([
                'status' => 'required|in:pending,processing,completed,cancelled,rejected',
            ]);
            $data['status'] = $request->status;
        }

        if ($request->has('vendor_note')) {
            $data['vendor_note'] = $request->vendor_note;
        }

        if (!empty($data)) {
            $order->update($data);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'vendor_note' => $order->vendor_note,
            ]);
        }

        return back()->with('success', 'Order updated.');
    }

    /**
     * Delete an order.
     */
    public function destroy(Order $order)
    {
        $orderCode = $order->order_code;
        $order->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('vendor.dashboard')
            ->with('success', "Order {$orderCode} deleted.");
    }
}
