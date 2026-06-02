<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display vendor's product catalog.
     */
    public function index(Request $request)
    {
        $products = Product::byVendor(Auth::id())
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $categories = $products->pluck('category')->unique()->sort()->values();

        return view('vendor.products.index', compact('products', 'categories'));
    }

    /**
     * Show the add product form.
     */
    public function create()
    {
        return view('vendor.products.create');
    }

    /**
     * Store a new product.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean',
        ]);

        $validated['vendor_id'] = Auth::id();
        $validated['is_available'] = $request->has('is_available');

        Product::create($validated);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product added successfully.');
    }

    /**
     * Show the edit product form.
     */
    public function edit(Product $product)
    {
        // Ensure vendor owns this product
        if ($product->vendor_id !== Auth::id()) {
            abort(403);
        }

        return view('vendor.products.edit', compact('product'));
    }

    /**
     * Update a product.
     */
    public function update(Request $request, Product $product)
    {
        if ($product->vendor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
            'is_available' => 'boolean',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $product->update($validated);

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product)
    {
        if ($product->vendor_id !== Auth::id()) {
            abort(403);
        }

        $product->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('vendor.products.index')
            ->with('success', 'Product deleted.');
    }
}
