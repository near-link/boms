@extends('layouts.customer', ['title' => $product->name])

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('shop.index') }}" style="color:var(--ctp-overlay0);font-size:0.8rem;text-decoration:none;">&larr; Back to Browse</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    {{-- Product image --}}
    <div class="card" style="display:flex;align-items:center;justify-content:center;height:320px;overflow:hidden;border-radius:var(--radius-sm);">
        @if($product->image)
            <img src="/images/products/{{ $product->image }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;">
        @else
            <div class="product-placeholder-icon" style="opacity:0.25;">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            </div>
        @endif
    </div>

    {{-- Product info --}}
    <div>
        <div class="product-card-vendor" style="margin-bottom:4px;">{{ $product->vendor->name }}</div>
        <h1 style="font-size:1.5rem;margin:0 0 4px;">{{ $product->name }}</h1>
        <div class="product-card-category" style="margin-bottom:12px;">{{ $product->category }}</div>

        <p style="color:var(--ctp-subtext0);font-size:0.85rem;line-height:1.5;margin-bottom:20px;">
            {{ $product->description }}
        </p>

        <div style="font-size:1.6rem;font-weight:700;color:var(--ctp-green);margin-bottom:20px;">
            RM {{ number_format($product->price, 2) }}
        </div>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px;">
            @if($product->average_rating > 0)
                <span style="color:var(--ctp-yellow);font-size:1rem;">★ {{ $product->average_rating }}</span>
                <span style="color:var(--ctp-overlay0);font-size:0.8rem;">({{ $product->reviews->count() }} reviews)</span>
            @else
                <span style="color:var(--ctp-overlay0);font-size:0.8rem;">No reviews yet</span>
            @endif
            <span class="badge {{ $product->is_available ? 'badge-completed' : 'badge-rejected' }}">
                {{ $product->is_available ? 'In Stock' : 'Out of Stock' }}
            </span>
        </div>

        @if($product->is_available)
        <form method="POST" action="{{ route('cart.add') }}" style="display:flex;gap:10px;align-items:center;">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <div style="display:flex;align-items:center;gap:4px;">
                <label style="font-size:0.8rem;color:var(--ctp-overlay0);">Qty:</label>
                <input type="number" name="qty" value="1" min="1" max="{{ $product->stock }}" class="form-input" style="width:70px;text-align:center;">
            </div>
            <button type="submit" class="btn btn-customer" style="flex:1;">Add to Cart</button>
        </form>
        @endif
    </div>
</div>

{{-- Reviews --}}
<div class="card" style="margin-top:24px;">
    <div class="card-body">
        <div class="section-label">Reviews ({{ $product->reviews->count() }})</div>
        @forelse($product->reviews as $review)
        <div style="border-bottom:1px solid var(--ctp-surface0);padding:12px 0;">
            <div style="display:flex;justify-content:space-between;align-items:center;">
                <span style="font-weight:600;font-size:0.85rem;">{{ $review->user->name }}</span>
                <span style="color:var(--ctp-yellow);font-size:0.8rem;">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
            </div>
            @if($review->comment)
            <p style="color:var(--ctp-subtext0);font-size:0.8rem;margin:4px 0 0;">{{ $review->comment }}</p>
            @endif
        </div>
        @empty
        <p style="color:var(--ctp-overlay0);font-size:0.8rem;padding:16px 0;">No reviews yet. Be the first to review!</p>
        @endforelse
    </div>
</div>

{{-- Related Products --}}
@if($related->count() > 0)
<div style="margin-top:24px;">
    <div class="section-label">More from {{ $product->vendor->name }}</div>
    <div class="product-grid shop-grid" style="margin-top:12px;">
        @foreach($related as $rel)
        <a href="{{ route('shop.product', $rel) }}" class="card product-card shop-product-card">
            <div class="product-card-img" style="min-height:80px;">
                @if($rel->image)
                    <img src="/images/products/{{ $rel->image }}" alt="{{ $rel->name }}">
                @else
                    <div class="product-placeholder-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                    </div>
                @endif
            </div>
            <div class="product-card-body">
                <div class="product-card-name">{{ $rel->name }}</div>
                <div class="product-card-price">RM {{ number_format($rel->price, 2) }}</div>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif
@endsection
