@extends('layouts.vendor', ['title' => 'Products'])

@section('content')
<div class="page-header" style="display:flex;justify-content:space-between;align-items:flex-start;">
    <div>
        <h1>Products</h1>
        <p>Manage your menu items and catalog.</p>
    </div>
    <a href="{{ route('vendor.products.create') }}" class="btn btn-vendor">+ Add Product</a>
</div>

@if($categories->count() > 0)
<div class="tabs" style="margin-bottom:20px;" id="catTabs">
    <button class="tab active" data-cat="all">All</button>
    @foreach($categories as $cat)
        <button class="tab" data-cat="{{ $cat }}">{{ $cat }}</button>
    @endforeach
</div>
@endif

<div class="product-grid">
    @foreach($products as $product)
    <div class="card product-card" data-category="{{ $product->category }}">
        <div class="product-card-img">
            @if($product->image)
                <img src="/images/products/{{ $product->image }}" alt="{{ $product->name }}">
            @else
                <div class="product-placeholder-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            @endif
        </div>
        <div class="product-card-body">
            <div class="product-card-category">{{ $product->category }}</div>
            <div class="product-card-name">{{ $product->name }}</div>
            <div class="product-card-desc">{{ Str::limit($product->description, 60) }}</div>
            <div class="product-card-footer">
                <span class="product-card-price">RM {{ number_format($product->price, 2) }}</span>
                <span class="badge {{ $product->is_available ? 'badge-completed' : 'badge-rejected' }}">
                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                </span>
            </div>
            <div class="product-card-meta">
                Stock: {{ $product->stock }} &bull; {{ $product->reviews->count() }} reviews
                @if($product->average_rating > 0)
                    &bull; ★ {{ $product->average_rating }}
                @endif
            </div>
            <div class="product-card-actions">
                <a href="{{ route('vendor.products.edit', $product) }}" class="btn btn-sm btn-secondary">Edit</a>
                <form method="POST" action="{{ route('vendor.products.destroy', $product) }}" style="margin:0;" onsubmit="return confirm('Delete {{ $product->name }}?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>

@if($products->isEmpty())
<div class="card" style="text-align:center;padding:40px;">
    <p style="color:var(--ctp-overlay0);">No products yet. Add your first product to get started.</p>
</div>
@endif
@endsection

@section('scripts')
<script>
    var catTabs = document.querySelectorAll('#catTabs .tab');
    catTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            catTabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            var cat = this.getAttribute('data-cat');
            document.querySelectorAll('.product-card').forEach(function(card) {
                card.style.display = (cat === 'all' || card.getAttribute('data-category') === cat) ? '' : 'none';
            });
        });
    });
</script>
@endsection
