<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManifestController extends Controller
{
    /**
     * Predefined campus delivery route order for sorting.
     */
    private array $routeOrder = [
        'Block A - Main Lobby' => 1,
        'Block B - Cafeteria' => 2,
        'Dewan Kuliah Utama' => 3,
        'Library Entrance' => 4,
        'Counter' => 5,
    ];

    /**
     * Display the delivery manifest grouped by time slot and location.
     */
    public function index()
    {
        $vendorName = Auth::user()->name;

        $orders = Order::forVendor($vendorName)
            ->where('status', 'processing')
            ->orderBy('time_slot')
            ->orderBy('delivery_location')
            ->get();

        // Group by time slot, then by location within each slot
        $manifest = [];
        $timeSlotOrder = [
            'Morning (8:00 - 10:00)' => 1,
            'Lunch (12:00 - 14:00)' => 2,
            'Evening (17:00 - 19:00)' => 3,
        ];

        foreach ($orders as $order) {
            $slot = $order->time_slot ?: 'Unscheduled';
            $location = $order->delivery_location ?: 'Unknown';

            if (!isset($manifest[$slot])) {
                $manifest[$slot] = [
                    'sort' => $timeSlotOrder[$slot] ?? 99,
                    'stops' => [],
                ];
            }

            if (!isset($manifest[$slot]['stops'][$location])) {
                $manifest[$slot]['stops'][$location] = [
                    'sort' => $this->routeOrder[$location] ?? 99,
                    'orders' => [],
                ];
            }

            $manifest[$slot]['stops'][$location]['orders'][] = [
                'id' => $order->id,
                'code' => $order->order_code,
                'customer' => $order->customer_display_name,
                'items' => $order->items,
                'total' => (float) $order->total,
                'notes' => $order->notes,
                'vendor_note' => $order->vendor_note,
            ];
        }

        // Sort time slots
        uasort($manifest, fn($a, $b) => $a['sort'] <=> $b['sort']);

        // Sort stops within each slot by route order
        foreach ($manifest as &$slot) {
            uasort($slot['stops'], fn($a, $b) => $a['sort'] <=> $b['sort']);
        }

        $totalOrders = $orders->count();
        $totalStops = collect($manifest)->sum(fn($s) => count($s['stops']));

        return view('vendor.manifest', compact('manifest', 'totalOrders', 'totalStops'));
    }

    /**
     * Bulk-complete orders for delivered stops.
     */
    public function complete(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'integer|exists:orders,id',
        ]);

        $vendorName = Auth::user()->name;

        $updated = Order::forVendor($vendorName)
            ->whereIn('id', $request->order_ids)
            ->where('status', 'processing')
            ->update(['status' => 'completed']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'updated' => $updated,
            ]);
        }

        return redirect()->route('vendor.manifest')
            ->with('success', "{$updated} order(s) marked as delivered.");
    }
}
