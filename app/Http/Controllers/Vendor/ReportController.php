<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    /**
     * Display analytics dashboard for the vendor.
     */
    public function index()
    {
        $vendorName = Auth::user()->name;

        // Summary stats
        $totalOrders = Order::forVendor($vendorName)->count();
        $totalRevenue = Order::forVendor($vendorName)->whereNotIn('status', ['cancelled', 'rejected'])->sum('total');
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;
        $totalProducts = Product::byVendor(Auth::id())->count();

        // Monthly revenue (last 6 months)
        $monthlyRevenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenue = Order::forVendor($vendorName)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->whereNotIn('status', ['cancelled', 'rejected'])
                ->sum('total');
            $monthlyRevenue[] = [
                'label' => $month->format('M'),
                'value' => (float) $revenue,
            ];
        }

        // Top products by revenue
        $allOrders = Order::forVendor($vendorName)
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->get();

        $productSales = [];
        foreach ($allOrders as $order) {
            foreach ($order->items as $item) {
                $key = $item['name'];
                if (!isset($productSales[$key])) {
                    $productSales[$key] = ['name' => $key, 'qty' => 0, 'revenue' => 0];
                }
                $productSales[$key]['qty'] += $item['qty'];
                $productSales[$key]['revenue'] += $item['qty'] * $item['price'];
            }
        }
        usort($productSales, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        $topProducts = array_slice($productSales, 0, 5);

        // Status breakdown
        $statusBreakdown = [
            'pending' => Order::forVendor($vendorName)->where('status', 'pending')->count(),
            'processing' => Order::forVendor($vendorName)->where('status', 'processing')->count(),
            'completed' => Order::forVendor($vendorName)->where('status', 'completed')->count(),
            'rejected' => Order::forVendor($vendorName)->where('status', 'rejected')->count(),
        ];

        return view('vendor.reports.index', compact(
            'totalOrders', 'totalRevenue', 'avgOrderValue', 'totalProducts',
            'monthlyRevenue', 'topProducts', 'statusBreakdown'
        ));
    }
}
