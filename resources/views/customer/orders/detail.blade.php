@extends('layouts.customer', ['title' => 'Order ' . $order->order_code])

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('customer.orders.index') }}" style="color:var(--ctp-overlay0);font-size:0.8rem;text-decoration:none;">&larr; Back to My Orders</a>
</div>

<div class="page-header">
    <h1>{{ $order->order_code }}</h1>
    <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
</div>

{{-- Status Timeline --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        @php
            $steps = ['pending' => 'Order Placed', 'processing' => 'Preparing', 'completed' => 'Completed'];
            $stepKeys = array_keys($steps);
            $currentIdx = array_search($order->status, $stepKeys);
            if ($currentIdx === false) $currentIdx = -1;
        @endphp
        <div class="order-timeline">
            @foreach($steps as $key => $label)
                @php $idx = array_search($key, $stepKeys); @endphp
                <div class="timeline-step {{ $idx <= $currentIdx ? 'active' : '' }}">
                    <div class="timeline-dot"></div>
                    <span>{{ $label }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
        <div class="card-body">
            <div class="section-label">Order Details</div>
            <div class="panel-info-grid">
                <div class="panel-info"><span class="panel-info-label">Vendor</span><span class="panel-info-value">{{ $order->vendor_name }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Location</span><span class="panel-info-value">{{ $order->delivery_location }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Date</span><span class="panel-info-value">{{ $order->delivery_date->format('d M Y') }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Time Slot</span><span class="panel-info-value">{{ $order->time_slot }}</span></div>
            </div>
            @if($order->vendor_note)
                <div style="margin-top:12px;padding:10px;background:var(--ctp-surface0);border-radius:var(--radius-sm);font-size:0.8rem;">
                    <strong>Vendor Note:</strong> {{ $order->vendor_note }}
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="section-label">Items</div>
            @foreach($order->items as $item)
            <div class="panel-item-row">
                <span>{{ $item['name'] }} x{{ $item['qty'] }}</span>
                <span>RM {{ number_format($item['qty'] * $item['price'], 2) }}</span>
            </div>
            @endforeach
            <div class="order-summary" style="margin-top:12px;">
                <div class="summary-row"><span>Subtotal</span><span>RM {{ number_format($order->subtotal, 2) }}</span></div>
                <div class="summary-row"><span>Delivery Fee</span><span>RM {{ number_format($order->delivery_fee, 2) }}</span></div>
                <div class="summary-row total"><span>Total</span><span>RM {{ number_format($order->total, 2) }}</span></div>
            </div>
        </div>
    </div>
</div>
@endsection
