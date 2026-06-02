@extends('layouts.customer', ['title' => 'Checkout'])

@section('content')
<div class="page-header">
    <h1>Checkout</h1>
    <p>Review and confirm your order.</p>
</div>

@if ($errors->any())
    <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
        @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
    </div>
@endif

<form method="POST" action="{{ route('checkout.store') }}">
    @csrf
    <div style="display:grid;grid-template-columns:1fr 380px;gap:24px;">
        {{-- Left: Form fields --}}
        <div>
            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div class="section-label">Delivery Details</div>

                    <div class="form-group">
                        <label class="form-label">Delivery Location</label>
                        <select name="delivery_location" class="form-select" required>
                            <option value="">Select location</option>
                            <option value="blkA" {{ old('delivery_location') === 'blkA' ? 'selected' : '' }}>Block A - Main Lobby</option>
                            <option value="blkB" {{ old('delivery_location') === 'blkB' ? 'selected' : '' }}>Block B - Cafeteria</option>
                            <option value="lib" {{ old('delivery_location') === 'lib' ? 'selected' : '' }}>Library Entrance</option>
                            <option value="dewan" {{ old('delivery_location') === 'dewan' ? 'selected' : '' }}>Dewan Kuliah Utama</option>
                        </select>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Delivery Date</label>
                            <input type="date" name="delivery_date" class="form-input" required value="{{ old('delivery_date', now()->toDateString()) }}" min="{{ now()->toDateString() }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Time Slot</label>
                            <select name="time_slot" class="form-select" required>
                                <option value="">Select time</option>
                                <option value="Morning (8:00 - 10:00)">Morning (8:00 - 10:00)</option>
                                <option value="Lunch (12:00 - 14:00)">Lunch (12:00 - 14:00)</option>
                                <option value="Evening (17:00 - 19:00)">Evening (17:00 - 19:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-textarea" rows="2" placeholder="Any special requests...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="section-label">Payment Method</div>
                    <div style="display:flex;gap:12px;">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <div class="payment-option-card">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                <span>Cash on Delivery</span>
                            </div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="online">
                            <div class="payment-option-card">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
                                <span>Online (Mock)</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Order summary --}}
        <div>
            <div class="card" style="position:sticky;top:24px;">
                <div class="card-body">
                    <div class="section-label">Order Summary</div>
                    @foreach($cartItems as $item)
                    <div class="panel-item-row">
                        <span style="font-size:0.8rem;">{{ $item->product->name }} x{{ $item->qty }}</span>
                        <span style="font-size:0.8rem;font-weight:600;">RM {{ number_format($item->qty * $item->product->price, 2) }}</span>
                    </div>
                    @endforeach

                    <div class="order-summary" style="margin-top:12px;">
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

                    <button type="submit" class="btn btn-customer btn-block" style="margin-top:16px;">Place Order</button>
                    <a href="{{ route('cart.index') }}" style="display:block;text-align:center;margin-top:8px;color:var(--ctp-overlay0);font-size:0.8rem;text-decoration:none;">&larr; Back to Cart</a>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
