@extends('layouts.customer', ['title' => 'Order Confirmed'])

@section('content')
<div style="text-align:center;padding:40px 0;">
    <div style="width:64px;height:64px;border-radius:50%;background:var(--ctp-green);color:var(--ctp-base);display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.5rem;">✓</div>
    <h1 style="font-size:1.5rem;margin-bottom:4px;">Order Placed!</h1>
    <p style="color:var(--ctp-subtext0);margin-bottom:24px;">Your order has been submitted successfully.</p>

    <div class="card" style="max-width:480px;margin:0 auto;text-align:left;">
        <div class="card-body">
            <div class="panel-info-grid">
                <div class="panel-info">
                    <span class="panel-info-label">Order Code</span>
                    <span class="panel-info-value" style="font-weight:700;color:var(--ctp-mauve);font-size:1.1rem;">{{ $order->order_code }}</span>
                </div>
                <div class="panel-info">
                    <span class="panel-info-label">Vendor</span>
                    <span class="panel-info-value">{{ $order->vendor_name }}</span>
                </div>
                <div class="panel-info">
                    <span class="panel-info-label">Location</span>
                    <span class="panel-info-value">{{ $order->delivery_location }}</span>
                </div>
                <div class="panel-info">
                    <span class="panel-info-label">Total</span>
                    <span class="panel-info-value" style="font-weight:700;">RM {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-top:24px;display:flex;gap:10px;justify-content:center;">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-customer">View My Orders</a>
        <a href="{{ route('shop.index') }}" class="btn btn-secondary">Continue Shopping</a>
    </div>
</div>
@endsection
