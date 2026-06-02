<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Show the product browsing page (home).
     */
    public function index(Request $request)
    {
        $query = Product::with('vendor', 'reviews')->available();

        // Category filter
        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        // Vendor filter
        if ($request->filled('vendor')) {
            $query->byVendor($request->vendor);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('name')->get();
        $categories = Product::available()->pluck('category')->unique()->sort()->values();
        $vendors = User::where('role', 'vendor')->get();

        return view('customer.home', compact('products', 'categories', 'vendors'));
    }

    /**
     * Show product detail page.
     */
    public function show(Product $product)
    {
        $product->load(['vendor', 'reviews.user']);

        // Related products from same vendor
        $related = Product::available()
            ->byVendor($product->vendor_id)
            ->where('id', '!=', $product->id)
            ->limit(4)
            ->get();

        return view('customer.product', compact('product', 'related'));
    }
}
