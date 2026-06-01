<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class OrderController extends Controller
{
    /**
     * Display the vendor dashboard with scoped orders (READ).
     */
    public function index(Request $request)
    {
        $vendorName = Auth::user()->name;
        $range = $request->input('range', 'today');

        // Date range
        $startDate = match ($range) {
            'week' => now()->subDays(6)->startOfDay(),
            'month' => now()->subDays(29)->startOfDay(),
            'all' => null,
            default => now()->startOfDay(),
        };

        $query = Order::with('customer')
            ->forVendor($vendorName)
            ->orderBy('created_at', 'desc');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $orders = $query->get();

        // Transform for JS
        $ordersJson = $orders->map(function ($o) {
            $itemSummary = collect($o->items)->map(fn($i) => $i['name'] . ' x' . $i['qty'])->join(', ');
            return [
                'id' => $o->id,
                'code' => $o->order_code,
                'customer' => $o->customer_display_name,
                'items' => $itemSummary,
                'total' => 'RM ' . number_format($o->total, 2),
                'totalRaw' => (float) $o->total,
                'time' => $o->created_at->format('g:i A'),
                'date' => $o->created_at->format('d M'),
                'status' => $o->status,
                'location' => $o->delivery_location,
                'timeSlot' => $o->time_slot,
                'notes' => $o->notes,
                'vendorNote' => $o->vendor_note,
                'itemsFull' => $o->items,
                'subtotal' => (float) $o->subtotal,
                'deliveryFee' => (float) $o->delivery_fee,
                'deliveryDate' => $o->delivery_date->format('d M Y'),
                'createdAt' => $o->created_at->format('d M Y, g:i A'),
            ];
        })->values();

        // Stats scoped to vendor
        $baseQuery = Order::forVendor($vendorName);
        $todayOrders = (clone $baseQuery)->whereDate('created_at', today())->count();
        $pendingCount = (clone $baseQuery)->where('status', 'pending')->count();
        $processingCount = (clone $baseQuery)->where('status', 'processing')->count();

        // Revenue for date range
        $revenueQuery = clone $baseQuery;
        if ($startDate) {
            $revenueQuery->where('created_at', '>=', $startDate);
        }
        $totalRevenue = (clone $revenueQuery)->whereNotIn('status', ['cancelled', 'rejected'])->sum('total');

        // Daily revenue for chart (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i);
            $dayRevenue = Order::forVendor($vendorName)
                ->whereDate('created_at', $day)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->sum('total');
            $chartData[] = [
                'label' => $day->format('D'),
                'value' => (float) $dayRevenue,
            ];
        }

        return view('dashboard', compact(
            'ordersJson', 'todayOrders', 'pendingCount', 'processingCount',
            'totalRevenue', 'chartData', 'range'
        ));
    }

    /**
     * Get order detail as JSON (for modal).
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
     * Show the customer order creation form.
     */
    public function create()
    {
        return view('orders.create');
    }

    /**
     * Show the vendor manual order form.
     */
    public function vendorCreate()
    {
        return view('vendor.create');
    }

    /**
     * Store a new order from customer (CREATE).
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
     * Store a vendor manual order (walk-in).
     */
    public function vendorStore(Request $request)
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

        return redirect()->route('dashboard')
            ->with('success', "Order {$order->order_code} created.");
    }

    /**
     * Show the tracking page for a specific order (READ).
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
     * Search for an order by code.
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
     * Update an order's status and/or vendor note (UPDATE).
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

    /**
     * API: Return stats for polling (new order count, etc).
     */
    public function stats(Request $request)
    {
        $vendorName = Auth::user()->name;
        $pendingCount = Order::forVendor($vendorName)->where('status', 'pending')->count();

        return response()->json([
            'pending' => $pendingCount,
        ]);
    }
}
