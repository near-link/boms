@extends('layouts.customer', ['title' => 'Cart'])

@section('content')
<div class="page-header">
    <h1>Shopping Cart</h1>
    <p>{{ $cartItems->count() }} item{{ $cartItems->count() !== 1 ? 's' : '' }} in your cart.</p>
</div>

@if(session('error'))
    <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
        {{ session('error') }}
    </div>
@endif

@if($cartItems->isEmpty())
    <div class="card" style="text-align:center;padding:40px;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ctp-overlay0)" stroke-width="1.5" style="margin-bottom:12px;"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>
        <p style="color:var(--ctp-overlay0);">Your cart is empty.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-customer" style="margin-top:12px;">Browse Menu</a>
    </div>
@else
    <div style="display:grid;grid-template-columns:1fr 320px;gap:20px;align-items:start;">
        {{-- Left: Cart items --}}
        <div class="card">
            <div class="card-body">
                @foreach($cartItems as $item)
                <div class="cart-item">
                    <div class="cart-item-info" style="display:flex;align-items:center;gap:12px;">
                        @if($item->product->image)
                            <img src="/images/products/{{ $item->product->image }}" alt="{{ $item->product->name }}" style="width:48px;height:48px;object-fit:cover;border-radius:4px;">
                        @else
                            <div style="width:48px;height:48px;background:var(--ctp-surface0);border-radius:4px;display:flex;align-items:center;justify-content:center;">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:0.5;"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
                            </div>
                        @endif
                        <div>
                            <div class="cart-item-name">{{ $item->product->name }}</div>
                            <div class="cart-item-vendor">{{ $item->product->vendor->name }}</div>
                            <div class="cart-item-price">RM {{ number_format($item->product->price, 2) }} each</div>
                        </div>
                    </div>
                    <div class="cart-item-controls">
                        <form method="POST" action="{{ route('cart.update', $item) }}" class="cart-qty-form">
                            @csrf @method('PUT')
                            <button type="button" class="qty-btn" onclick="changeQty(this, -1)">−</button>
                            <input type="number" name="qty" value="{{ $item->qty }}" min="1" class="qty-input" onchange="this.form.submit()">
                            <button type="button" class="qty-btn" onclick="changeQty(this, 1)">+</button>
                        </form>
                        <span class="cart-item-line-total">RM {{ number_format($item->qty * $item->product->price, 2) }}</span>
                        <form method="POST" action="{{ route('cart.remove', $item) }}" style="margin:0;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" style="padding:4px 8px;">✕</button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Right: Order summary --}}
        <div class="card" style="position:sticky;top:24px;">
            <div class="card-body">
                <div class="section-label">Order Summary</div>
                <div class="order-summary" style="margin-top:0;padding-top:0;border-top:none;">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>RM {{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span>RM {{ number_format($deliveryFee, 2) }}</span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>RM {{ number_format($total, 2) }}</span>
                    </div>
                </div>
                <a href="{{ route('checkout.index') }}" class="btn btn-customer btn-block" style="margin-top:16px;">Proceed to Checkout</a>
                <a href="{{ route('shop.index') }}" style="display:block;text-align:center;margin-top:8px;color:var(--ctp-overlay0);font-size:0.8rem;text-decoration:none;">&larr; Continue Shopping</a>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    function changeQty(btn, delta) {
        var input = btn.parentElement.querySelector('.qty-input');
        var newVal = parseInt(input.value) + delta;
        if (newVal < 1) newVal = 1;
        input.value = newVal;
        btn.closest('form').submit();
    }
</script>
@endsection
