<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BOMS - Vendor dashboard for managing campus orders">
    <title>BOMS - Vendor Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="vendor-theme">
    <nav class="navbar">
        <div class="navbar-brand">BOMS <span>vendor</span></div>
        <div class="navbar-title">Dashboard</div>
        <div class="navbar-user">
            <span class="navbar-role-tag">Vendor</span>
            <span>{{ Auth::user()->name }}</span>
            <div class="navbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) . strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="padding:4px 10px;font-size:0.65rem;">Logout</button>
            </form>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>Dashboard</h1>
            <p>Manage and update incoming orders.</p>
        </div>

        @if (session('success'))
            <div style="background:rgba(166,227,161,0.12);border:1px solid var(--ctp-green);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-green);font-size:0.8rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="stat-grid">
            <div class="card stat-card">
                <div class="stat-label">Orders Today</div>
                <div class="stat-value">{{ $todayOrders }}</div>
                <div class="stat-sub">Total orders today</div>
            </div>
            <div class="card stat-card">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color:var(--ctp-yellow);">{{ $pendingCount }}</div>
                <div class="stat-sub">Needs attention</div>
            </div>
            <div class="card stat-card">
                <div class="stat-label">Processing</div>
                <div class="stat-value" style="color:var(--ctp-blue);">{{ $processingCount }}</div>
                <div class="stat-sub">In progress</div>
            </div>
            <div class="card stat-card">
                <div class="stat-label">Revenue</div>
                <div class="stat-value" style="color:var(--ctp-green);">RM {{ number_format($todayRevenue, 2) }}</div>
                <div class="stat-sub">Today's earnings</div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="filter-bar">
                    <div class="tabs" id="statusTabs">
                        <button class="tab active" data-filter="all">All</button>
                        <button class="tab" data-filter="pending">Pending</button>
                        <button class="tab" data-filter="processing">Processing</button>
                        <button class="tab" data-filter="completed">Completed</button>
                    </div>
                    <div class="search-box">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
                        <input type="text" placeholder="Search..." id="searchInput">
                    </div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Items</th>
                                <th>Total</th>
                                <th>Time</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="ordersBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        var orders = @json($ordersJson);

        function renderOrders(filter, search) {
            var tbody = document.getElementById('ordersBody');
            var filtered = orders.filter(function(o) {
                var matchFilter = filter === 'all' || o.status === filter;
                var matchSearch = o.customer.toLowerCase().indexOf(search.toLowerCase()) !== -1 || o.code.toLowerCase().indexOf(search.toLowerCase()) !== -1;
                return matchFilter && matchSearch;
            });

            var html = '';
            for (var i = 0; i < filtered.length; i++) {
                var o = filtered[i];
                var actionHtml = '<div style="display:flex;gap:6px;align-items:center;min-width:156px;">';
                if (o.status === 'pending') {
                    actionHtml += '<button class="btn btn-sm btn-vendor" style="min-width:72px;" onclick="updateStatus(' + o.id + ', \'processing\')">Accept</button>' +
                                  '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="deleteOrder(' + o.id + ', \'' + o.code + '\')">Delete</button>';
                } else if (o.status === 'processing') {
                    actionHtml += '<button class="btn btn-sm btn-vendor" style="min-width:72px;" onclick="updateStatus(' + o.id + ', \'completed\')">Done</button>' +
                                  '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="deleteOrder(' + o.id + ', \'' + o.code + '\')">Delete</button>';
                } else {
                    actionHtml += '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="deleteOrder(' + o.id + ', \'' + o.code + '\')">Delete</button>';
                }
                actionHtml += '</div>';

                html += '<tr>' +
                    '<td><span class="order-id">' + o.code + '</span></td>' +
                    '<td>' + o.customer + '</td>' +
                    '<td style="color:var(--ctp-subtext0);font-size:0.75rem;">' + o.items + '</td>' +
                    '<td style="font-weight:600;">' + o.total + '</td>' +
                    '<td style="color:var(--ctp-overlay0);font-size:0.75rem;">' + o.time + '</td>' +
                    '<td><span class="badge badge-' + o.status + '">' + o.status.charAt(0).toUpperCase() + o.status.slice(1) + '</span></td>' +
                    '<td style="min-width:156px;">' + actionHtml + '</td>' +
                    '</tr>';
            }
            tbody.innerHTML = html;
        }

        function updateStatus(orderId, newStatus) {
            fetch('/orders/' + orderId, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    for (var i = 0; i < orders.length; i++) {
                        if (orders[i].id === orderId) {
                            orders[i].status = newStatus;
                            break;
                        }
                    }
                    var activeTab = document.querySelector('.tab.active').getAttribute('data-filter');
                    renderOrders(activeTab, document.getElementById('searchInput').value);
                }
            });
        }

        function deleteOrder(orderId, orderCode) {
            if (!confirm('Delete order ' + orderCode + '?')) return;
            fetch('/orders/' + orderId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(function(res) { return res.json(); })
            .then(function(data) {
                if (data.success) {
                    orders = orders.filter(function(o) { return o.id !== orderId; });
                    var activeTab = document.querySelector('.tab.active').getAttribute('data-filter');
                    renderOrders(activeTab, document.getElementById('searchInput').value);
                }
            });
        }

        var tabs = document.querySelectorAll('#statusTabs .tab');
        for (var i = 0; i < tabs.length; i++) {
            tabs[i].addEventListener('click', function() {
                for (var j = 0; j < tabs.length; j++) { tabs[j].classList.remove('active'); }
                this.classList.add('active');
                renderOrders(this.getAttribute('data-filter'), document.getElementById('searchInput').value);
            });
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            var activeTab = document.querySelector('.tab.active').getAttribute('data-filter');
            renderOrders(activeTab, this.value);
        });

        renderOrders('all', '');
    </script>
</body>
</html>
