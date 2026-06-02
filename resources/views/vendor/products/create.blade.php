@extends('layouts.vendor', ['title' => 'Add Product'])

@section('content')
<div class="page-header">
    <h1>Add Product</h1>
    <p>Add a new item to your menu.</p>
</div>

@if ($errors->any())
    <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('vendor.products.store') }}">
    @csrf
    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Product Name</label>
                <input type="text" name="name" class="form-input" placeholder="e.g. Nasi Lemak Special" required value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-textarea" rows="3" placeholder="Describe the product...">{{ old('description') }}</textarea>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Price (RM)</label>
                    <input type="number" name="price" class="form-input" step="0.01" min="0" placeholder="0.00" required value="{{ old('price') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <input type="text" name="category" class="form-input" placeholder="e.g. Rice, Drinks" required value="{{ old('category') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Stock</label>
                    <input type="number" name="stock" class="form-input" min="0" value="{{ old('stock', 100) }}" required>
                </div>
                <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px;">
                    <input type="checkbox" name="is_available" id="isAvailable" value="1" {{ old('is_available', true) ? 'checked' : '' }} style="width:18px;height:18px;accent-color:var(--ctp-mauve);">
                    <label for="isAvailable" style="font-size:0.8rem;cursor:pointer;">Available for order</label>
                </div>
            </div>
        </div>
    </div>
    <div style="display:flex;gap:10px;">
        <a href="{{ route('vendor.products.index') }}" class="btn btn-secondary">Cancel</a>
        <button type="submit" class="btn btn-vendor">Add Product</button>
    </div>
</form>
@endsection
