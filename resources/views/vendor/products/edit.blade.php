@extends('layouts.vendor', ['title' => 'Edit Product'])

@section('content')
<div class="page-header">
    <h1>Edit Product</h1>
    <p>Update {{ $product->name }}.</p>
</div>

@if ($errors->any())
    <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('vendor.products.update', $product) }}">
    @csrf @method('PUT')
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-input" required value="{{ old('name', $product->name) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="3">{{ old('description', $product->description) }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Price (RM)</label>
                    <input type="number" name="price" class="form-input" step="0.01" min="0" required value="{{ old('price', $product->price) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-input" required value="{{ old('category', $product->category) }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-input" min="0" value="{{ old('stock', $product->stock) }}" required>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px;">
                    <input type="checkbox" name="is_available" id="isAvailable" value="1" {{ old('is_available', $product->is_available) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--ctp-mauve);">
                    <label for="isAvailable" style="font-size:0.8rem;cursor:pointer;">Available for order</label>
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('vendor.products.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-vendor">Save Changes</button>
    </div>
</form>
@endsection
