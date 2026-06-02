@extends('layouts.customer', ['title' => 'Browse'])

@section('content')
<div class="page-header">
    <h1>Browse Menu</h1>
    <p>Discover delicious food from campus vendors.</p>
</div>

{{-- Search & Filters --}}
<div class="filter-bar" style="margin-bottom:20px;flex-wrap:wrap;">
    <div class="search-box" style="min-width:220px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
        <input type="text" placeholder="Search products..." id="shopSearch" value="{{ request('search') }}">
    </div>
    <div class="tabs" id="shopCatTabs">
        <button class="tab {{ !request('category') ? 'active' : '' }}" data-cat="">All</button>
        @foreach($categories as $cat)
            <button class="tab {{ request('category') === $cat ? 'active' : '' }}" data-cat="{{ $cat }}">{{ $cat }}</button>
        @endforeach
    </div>
</div>

{{-- Vendor Filter Pills --}}
<div style="display:flex;gap:8px;margin-bottom:20px;flex-wrap:wrap;">
    <a href="{{ route('shop.index') }}" class="pill {{ !request('vendor') ? 'pill-active' : '' }}">All Vendors</a>
    @foreach($vendors as $v)
        <a href="{{ route('shop.index', ['vendor' => $v->id]) }}" class="pill {{ request('vendor') == $v->id ? 'pill-active' : '' }}">{{ $v->name }}</a>
    @endforeach
</div>

{{-- Product Grid --}}
<div class="product-grid shop-grid">
    @foreach($products as $product)
    <a href="{{ route('shop.product', $product) }}" class="card product-card shop-product-card" data-category="{{ $product->category }}">
        <div class="product-card-img">
            @if($product->image)
                <img src="/images/products/{{ $product->image }}" alt="{{ $product->name }}">
            @else
                <div class="product-placeholder-icon">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                </div>
            @endif
        </div>
        <div class="product-card-body">
            <div class="product-card-vendor">{{ $product->vendor->name }}</div>
            <div class="product-card-name">{{ $product->name }}</div>
            <div class="product-card-desc">{{ Str::limit($product->description, 50) }}</div>
            <div class="product-card-footer">
                <span class="product-card-price">RM {{ number_format($product->price, 2) }}</span>
                @if($product->reviews->count() > 0)
                    <span class="product-card-rating">★ {{ $product->average_rating }} ({{ $product->reviews->count() }})</span>
                @endif
            </div>
        </div>
    </a>
    @endforeach
</div>

@if($products->isEmpty())
<div class="card" style="text-align:center;padding:40px;">
    <p style="color:var(--ctp-overlay0);">No products found. Try a different search or filter.</p>
</div>
@endif
@endsection

@section('scripts')
<script>
    document.getElementById('shopSearch').addEventListener('input', function() {
        var search = this.value.toLowerCase();
        document.querySelectorAll('.shop-product-card').forEach(function(card) {
            var text = card.textContent.toLowerCase();
            card.style.display = text.indexOf(search) > -1 ? '' : 'none';
        });
    });

    var catTabs = document.querySelectorAll('#shopCatTabs .tab');
    catTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            catTabs.forEach(function(t) { t.classList.remove('active'); });
            this.classList.add('active');
            var cat = this.getAttribute('data-cat');
            document.querySelectorAll('.shop-product-card').forEach(function(card) {
                card.style.display = (!cat || card.getAttribute('data-category') === cat) ? '' : 'none';
            });
        });
    });
</script>
@endsection
