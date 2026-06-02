<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the vendor dashboard with scoped orders.
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

        return view('vendor.dashboard', compact(
            'ordersJson', 'todayOrders', 'pendingCount', 'processingCount',
            'totalRevenue', 'chartData', 'range'
        ));
    }

    /**
     * API: Return stats for polling.
     */
    public function stats()
    {
        $vendorName = Auth::user()->name;
        $pendingCount = Order::forVendor($vendorName)->where('status', 'pending')->count();

        return response()->json(['pending' => $pendingCount]);
    }
}
