@extends('layouts.vendor', ['title' => 'Customers'])

@section('content')
<div class="page-header">
    <h1>Customers</h1>
    <p>View customers who have ordered from your store.</p>
</div>

<div class="card">
    <div class="card-body">
        <div class="filter-bar">
            <div class="search-box">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                <input type="text" placeholder="Search customers..." id="customerSearch">
            </div>
            <span style="font-size:0.8rem;color:var(--ctp-overlay0);">{{ $allCustomers->count() }} customers</span>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                        <th>Last Order</th>
                    </tr>
                </thead>
                <tbody id="customerBody">
                    @foreach($allCustomers as $c)
                    <tr class="customer-row">
                        <td style="font-weight:600;">{{ $c['name'] }}</td>
                        <td style="color:var(--ctp-subtext0);font-size:0.8rem;">{{ $c['email'] }}</td>
                        <td style="color:var(--ctp-subtext0);font-size:0.8rem;">{{ $c['phone'] ?? '-' }}</td>
                        <td><span class="badge badge-processing">{{ $c['totalOrders'] }}</span></td>
                        <td style="font-weight:600;color:var(--ctp-green);">RM {{ number_format($c['totalSpent'], 2) }}</td>
                        <td style="color:var(--ctp-overlay0);font-size:0.8rem;">{{ $c['lastOrder'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.getElementById('customerSearch').addEventListener('input', function() {
        var search = this.value.toLowerCase();
        document.querySelectorAll('.customer-row').forEach(function(row) {
            row.style.display = row.textContent.toLowerCase().indexOf(search) > -1 ? '' : 'none';
        });
    });
</script>
@endsection
