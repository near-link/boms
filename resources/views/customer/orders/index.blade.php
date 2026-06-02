@extends('layouts.customer', ['title' => 'My Orders'])

@section('content')
<div class="page-header">
    <h1>My Orders</h1>
    <p>Track your order history and status.</p>
</div>

@if($orders->isEmpty())
    <div class="card" style="text-align:center;padding:40px;">
        <p style="color:var(--ctp-overlay0);">You haven't placed any orders yet.</p>
        <a href="{{ route('shop.index') }}" class="btn btn-customer" style="margin-top:12px;">Browse Menu</a>
    </div>
@else
    <div class="card">
        <div class="card-body">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Order Code</th>
                            <th>Vendor</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td><span class="order-id">{{ $order->order_code }}</span></td>
                            <td>{{ $order->vendor_name }}</td>
                            <td style="color:var(--ctp-subtext0);font-size:0.75rem;">
                                {{ collect($order->items)->map(fn($i) => $i['name'].' x'.$i['qty'])->join(', ') }}
                            </td>
                            <td style="font-weight:600;">RM {{ number_format($order->total, 2) }}</td>
                            <td style="font-size:0.8rem;color:var(--ctp-overlay0);">{{ $order->created_at->format('d M Y') }}</td>
                            <td><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                            <td><a href="{{ route('customer.orders.show', $order) }}" class="btn btn-sm btn-secondary">View</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif
@endsection
