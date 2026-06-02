@extends('layouts.customer', ['title' => 'My Account'])

@section('content')
<div class="page-header">
    <h1>My Account</h1>
    <p>Welcome back, {{ $user->name }}.</p>
</div>

<div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
    <div class="card stat-card">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $orderCount }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Total Spent</div>
        <div class="stat-value" style="color:var(--ctp-green);">RM {{ number_format($totalSpent, 2) }}</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Member Since</div>
        <div class="stat-value" style="font-size:1rem;">{{ $user->created_at->format('M Y') }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
    <div class="card">
        <div class="card-body">
            <div class="section-label">Profile</div>
            <div class="panel-info-grid">
                <div class="panel-info"><span class="panel-info-label">Name</span><span class="panel-info-value">{{ $user->name }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Email</span><span class="panel-info-value">{{ $user->email }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Phone</span><span class="panel-info-value">{{ $user->phone ?? 'Not set' }}</span></div>
                <div class="panel-info"><span class="panel-info-label">Address</span><span class="panel-info-value">{{ $user->address ?? 'Not set' }}</span></div>
            </div>
            <a href="{{ route('customer.settings') }}" class="btn btn-sm btn-secondary" style="margin-top:12px;">Edit Profile</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="section-label">Recent Orders</div>
            @forelse($recentOrders as $order)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--ctp-surface0);">
                <div>
                    <span class="order-id" style="font-size:0.75rem;">{{ $order->order_code }}</span>
                    <span style="color:var(--ctp-overlay0);font-size:0.7rem;margin-left:6px;">{{ $order->created_at->format('d M') }}</span>
                </div>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
            @empty
            <p style="color:var(--ctp-overlay0);font-size:0.8rem;padding:16px 0;">No orders yet.</p>
            @endforelse
            @if($recentOrders->count() > 0)
            <a href="{{ route('customer.orders.index') }}" style="display:block;margin-top:8px;color:var(--ctp-blue);font-size:0.8rem;">View all orders &rarr;</a>
            @endif
        </div>
    </div>
</div>
@endsection
