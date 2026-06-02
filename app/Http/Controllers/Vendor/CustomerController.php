<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display customers who have ordered from this vendor.
     */
    public function index()
    {
        $vendorName = Auth::user()->name;

        // Get unique customers from orders
        $customerIds = Order::forVendor($vendorName)
            ->whereNotNull('customer_id')
            ->pluck('customer_id')
            ->unique();

        $customers = User::whereIn('id', $customerIds)->get()->map(function ($customer) use ($vendorName) {
            $orders = Order::forVendor($vendorName)->where('customer_id', $customer->id);
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'totalOrders' => (clone $orders)->count(),
                'totalSpent' => (clone $orders)->whereNotIn('status', ['cancelled', 'rejected'])->sum('total'),
                'lastOrder' => (clone $orders)->latest()->first()?->created_at?->format('d M Y'),
            ];
        });

        // Walk-in customers
        $walkIns = Order::forVendor($vendorName)
            ->whereNull('customer_id')
            ->whereNotNull('customer_name')
            ->get()
            ->groupBy('customer_name')
            ->map(function ($orders, $name) {
                return [
                    'id' => null,
                    'name' => $name,
                    'email' => '-',
                    'phone' => '-',
                    'totalOrders' => $orders->count(),
                    'totalSpent' => $orders->whereNotIn('status', ['cancelled', 'rejected'])->sum('total'),
                    'lastOrder' => $orders->sortByDesc('created_at')->first()?->created_at?->format('d M Y'),
                ];
            })->values();

        $allCustomers = $customers->concat($walkIns)->sortByDesc('totalSpent')->values();

        return view('vendor.customers.index', compact('allCustomers'));
    }
}
