@extends('layouts.vendor', ['title' => 'Reports'])

@section('content')
<div class="page-header">
    <h1>Reports</h1>
    <p>Sales analytics and performance overview.</p>
</div>

{{-- Summary stats --}}
<div class="stat-grid" style="grid-template-columns:repeat(4,1fr);">
    <div class="card stat-card">
        <div class="stat-label">Total Orders</div>
        <div class="stat-value">{{ $totalOrders }}</div>
        <div class="stat-sub">All time</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value" style="color:var(--ctp-green);">RM {{ number_format($totalRevenue, 2) }}</div>
        <div class="stat-sub">All time</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Avg Order Value</div>
        <div class="stat-value" style="color:var(--ctp-blue);">RM {{ number_format($avgOrderValue, 2) }}</div>
        <div class="stat-sub">Per order</div>
    </div>
    <div class="card stat-card">
        <div class="stat-label">Products</div>
        <div class="stat-value" style="color:var(--ctp-mauve);">{{ $totalProducts }}</div>
        <div class="stat-sub">In catalog</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
    {{-- Monthly revenue chart --}}
    <div class="card chart-card" style="min-height:220px;">
        <div class="chart-title">Monthly Revenue (6 months)</div>
        <div class="chart-bars">
            @php $maxRev = max(array_column($monthlyRevenue, 'value')); if ($maxRev == 0) $maxRev = 1; @endphp
            @foreach ($monthlyRevenue as $m)
                <div class="chart-bar-group">
                    <div class="chart-bar-value">{{ $m['value'] > 0 ? 'RM ' . number_format($m['value'], 0) : '' }}</div>
                    <div class="chart-bar" style="height: {{ ($m['value'] / $maxRev) * 100 }}%;"></div>
                    <div class="chart-bar-label">{{ $m['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Order status breakdown --}}
    <div class="card" style="padding:16px;">
        <div class="chart-title" style="margin-bottom:16px;">Order Status Breakdown</div>
        @foreach($statusBreakdown as $status => $count)
            <div style="display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid var(--ctp-surface0);">
                <span class="badge badge-{{ $status }}">{{ ucfirst($status) }}</span>
                <span style="font-weight:600;">{{ $count }}</span>
            </div>
        @endforeach
    </div>
</div>

{{-- Top products --}}
<div class="card">
    <div class="card-body">
        <div class="section-label">Top Products by Revenue</div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Qty Sold</th>
                        <th>Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topProducts as $i => $p)
                    <tr>
                        <td style="color:var(--ctp-overlay0);">{{ $i + 1 }}</td>
                        <td style="font-weight:600;">{{ $p['name'] }}</td>
                        <td>{{ $p['qty'] }}</td>
                        <td style="font-weight:600;color:var(--ctp-green);">RM {{ number_format($p['revenue'], 2) }}</td>
                    </tr>
                    @endforeach
                    @if(empty($topProducts))
                    <tr><td colspan="4" style="text-align:center;color:var(--ctp-overlay0);padding:20px;">No sales data yet.</td></tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
