<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class PrepListController extends Controller
{
    /**
     * Display the predictive prep list for a target date.
     */
    public function index(Request $request)
    {
        $vendorName = Auth::user()->name;
        $vendorId = Auth::id();

        // Target date (defaults to tomorrow)
        $targetDate = $request->input('date')
            ? Carbon::parse($request->input('date'))
            : now()->addDay();

        $targetDayOfWeek = $targetDate->dayOfWeek; // 0=Sun, 6=Sat
        $targetDayName = $targetDate->format('l'); // e.g. "Tuesday"

        // Look back 4 weeks for the same day of week
        $weeksBack = 4;
        $historicalDates = [];
        for ($i = 1; $i <= $weeksBack; $i++) {
            $historicalDates[] = $targetDate->copy()->subWeeks($i);
        }

        // Get all completed orders for this vendor on those historical dates
        $historicalOrders = Order::forVendor($vendorName)
            ->where('status', 'completed')
            ->where(function ($query) use ($historicalDates) {
                foreach ($historicalDates as $date) {
                    $query->orWhereDate('created_at', $date->toDateString());
                }
            })
            ->get();

        // Aggregate item quantities per product name across all historical orders
        $productAggregates = [];
        $weeklyBreakdown = []; // For sparkline data

        foreach ($historicalOrders as $order) {
            $orderWeek = Carbon::parse($order->created_at)->diffInWeeks($targetDate);

            if (is_array($order->items)) {
                foreach ($order->items as $item) {
                    $name = $item['name'] ?? 'Unknown';
                    $qty = (int) ($item['qty'] ?? 1);

                    if (!isset($productAggregates[$name])) {
                        $productAggregates[$name] = [
                            'total_qty' => 0,
                            'weeks_appeared' => [],
                            'weekly_data' => array_fill(1, $weeksBack, 0),
                        ];
                    }

                    $productAggregates[$name]['total_qty'] += $qty;
                    $productAggregates[$name]['weeks_appeared'][$orderWeek] = true;
                    $productAggregates[$name]['weekly_data'][$orderWeek] = ($productAggregates[$name]['weekly_data'][$orderWeek] ?? 0) + $qty;
                }
            }
        }

        // Build predictions
        $predictions = [];
        foreach ($productAggregates as $name => $data) {
            $weeksWithData = count($data['weeks_appeared']);
            $avgQty = $weeksWithData > 0 ? $data['total_qty'] / $weeksWithData : 0;
            $recommended = (int) ceil($avgQty * 1.2); // 20% buffer

            // Confidence level
            if ($weeksWithData >= 4) {
                $confidence = 'high';
            } elseif ($weeksWithData >= 2) {
                $confidence = 'medium';
            } else {
                $confidence = 'low';
            }

            // Try to match to a product for category/price info
            $product = Product::where('vendor_id', $vendorId)
                ->where('name', 'like', "%{$name}%")
                ->first();

            $predictions[] = [
                'name' => $name,
                'category' => $product->category ?? 'Uncategorized',
                'avg_qty' => round($avgQty, 1),
                'recommended' => $recommended,
                'confidence' => $confidence,
                'weeks_data' => $weeksWithData,
                'weekly_breakdown' => array_values($data['weekly_data']),
                'total_historical' => $data['total_qty'],
            ];
        }

        // Sort by recommended quantity (highest first)
        usort($predictions, fn($a, $b) => $b['recommended'] <=> $a['recommended']);

        $totalItems = array_sum(array_column($predictions, 'recommended'));
        $hasData = count($predictions) > 0;

        return view('vendor.prep-list', compact(
            'predictions', 'targetDate', 'targetDayName',
            'totalItems', 'hasData', 'weeksBack'
        ));
    }
}
