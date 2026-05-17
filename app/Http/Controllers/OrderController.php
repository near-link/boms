<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display the vendor dashboard with all orders (READ).
     */
    public function index(Request $request)
    {
        $query = Order::with('customer')->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by customer name or order code
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $orders = $query->get();

        // Transform orders for JS consumption
        $ordersJson = $orders->map(function ($o) {
            $itemSummary = collect($o->items)->map(function ($i) {
                return $i['name'] . ' x' . $i['qty'];
            })->join(', ');
            return [
                'id' => $o->id,
                'code' => $o->order_code,
                'customer' => $o->customer->name,
                'items' => $itemSummary,
                'total' => 'RM ' . number_format($o->total, 2),
                'time' => $o->created_at->format('g:i A'),
                'status' => $o->status,
            ];
        })->values();

        // Stats
        $todayOrders = Order::whereDate('created_at', today())->count();
        $pendingCount = Order::where('status', 'pending')->count();
        $processingCount = Order::where('status', 'processing')->count();
        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', '!=', 'cancelled')
            ->sum('total');

        return view('dashboard', compact(
            'orders', 'ordersJson', 'todayOrders', 'pendingCount', 'processingCount', 'todayRevenue'
        ));
    }

    /**
     * Show the order creation form (CREATE - form).
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Store a new order (CREATE - save).
     */
    public function store(Request $request)
    {
        $request->validate([
            'delivery_location' => 'required|string',
            'vendor_name' => 'required|string',
            'delivery_date' => 'required|date',
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

        // Map location codes to display names
        $locationNames = [
            'blkA' => 'Block A - Main Lobby',
            'blkB' => 'Block B - Cafeteria',
            'lib' => 'Library Entrance',
            'dewan' => 'Dewan Kuliah Utama',
        ];

        $order = Order::create([
            'order_code' => Order::generateOrderCode(),
            'customer_id' => Auth::id(),
            'vendor_name' => $request->vendor_name,
            'delivery_location' => $locationNames[$request->delivery_location] ?? $request->delivery_location,
            'delivery_date' => $request->delivery_date,
            'time_slot' => $request->time_slot,
            'items' => $items,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return redirect()->route('orders.track', $order->order_code)
            ->with('success', 'Order submitted successfully!');
    }

    /**
     * Show the tracking page for a specific order (READ - single).
     */
    public function track(Request $request, $orderCode = null)
    {
        $order = null;

        if ($orderCode) {
            $order = Order::with('customer')->where('order_code', $orderCode)->first();
        }

        return view('orders.track', compact('order'));
    }

    /**
     * Search for an order by code (READ - search).
     */
    public function search(Request $request)
    {
        $code = strtoupper(trim($request->input('order_code', '')));
        if ($code) {
            return redirect()->route('orders.track', $code);
        }
        return redirect()->route('orders.track.form');
    }

    /**
     * Update an order's status (UPDATE).
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $order->status]);
        }

        return back()->with('success', 'Order status updated.');
    }

    /**
     * Delete an order (DELETE).
     */
    public function destroy(Order $order)
    {
        $orderCode = $order->order_code;
        $order->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('dashboard')
            ->with('success', "Order {$orderCode} deleted.");
    }
}
