@extends('layouts.vendor', ['title' => 'Dashboard'])

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Manage and update incoming orders.</p>
</div>

{{-- Date range selector --}}
<div class="filter-bar" style="margin-bottom:16px;">
    <div class="tabs" id="rangeTabs">
        <button class="tab {{ $range === 'today' ? 'active' : '' }}" data-range="today">Today</button>
        <button class="tab {{ $range === 'week' ? 'active' : '' }}" data-range="week">This Week</button>
        <button class="tab {{ $range === 'month' ? 'active' : '' }}" data-range="month">This Month</button>
        <button class="tab {{ $range === 'all' ? 'active' : '' }}" data-range="all">All Time</button>
    </div>
    <div>
        <a href="{{ route('vendor.orders.create') }}" class="btn btn-sm btn-vendor">+ New Order</a>
    </div>
</div>

<div id="pendingAlert" class="pending-alert" style="display:none;margin-bottom:16px;">
    <span class="pulse-dot"></span>
    <span id="pendingAlertText"></span>
</div>

{{-- Stats + Chart row --}}
<div class="stats-chart-row">
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
            <div class="stat-value" style="color:var(--ctp-green);">RM {{ number_format($totalRevenue, 2) }}</div>
            <div class="stat-sub">For selected period</div>
        </div>
    </div>

    <div class="card chart-card">
        <div class="chart-title">Revenue (7 days)</div>
        <div class="chart-bars">
            @php
                $maxRevenue = max(array_column($chartData, 'value'));
                if ($maxRevenue == 0) $maxRevenue = 1;
            @endphp
            @foreach ($chartData as $day)
                <div class="chart-bar-group">
                    <div class="chart-bar-value">{{ $day['value'] > 0 ? 'RM ' . number_format($day['value'], 0) : '' }}</div>
                    <div class="chart-bar" style="height: {{ ($day['value'] / $maxRevenue) * 100 }}%;"></div>
                    <div class="chart-bar-label">{{ $day['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Orders table --}}
<div class="card">
    <div class="card-body">
        <div class="filter-bar">
            <div class="tabs" id="statusTabs">
                <button class="tab active" data-filter="all">All</button>
                <button class="tab" data-filter="pending">Pending</button>
                <button class="tab" data-filter="processing">Processing</button>
                <button class="tab" data-filter="completed">Completed</button>
                <button class="tab" data-filter="rejected">Rejected</button>
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
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="ordersBody"></tbody>
            </table>
        </div>

        <div class="pagination" id="pagination"></div>
    </div>
</div>

{{-- Order Detail Panel --}}
<div class="panel-overlay" id="panelOverlay"></div>
<div class="detail-panel" id="detailPanel">
    <div class="panel-header">
        <h3 id="panelTitle">Order Detail</h3>
        <button class="panel-close" id="panelClose">&times;</button>
    </div>
    <div class="panel-body" id="panelBody"></div>
</div>
@endsection

@section('scripts')
<script>
    var orders = @json($ordersJson);
    var currentPage = 1;
    var perPage = 10;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function renderOrders(filter, search) {
        var filtered = orders.filter(function(o) {
            var matchFilter = filter === 'all' || o.status === filter;
            var matchSearch = o.customer.toLowerCase().indexOf(search.toLowerCase()) !== -1 ||
                              o.code.toLowerCase().indexOf(search.toLowerCase()) !== -1;
            return matchFilter && matchSearch;
        });

        var totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = totalPages || 1;
        var start = (currentPage - 1) * perPage;
        var paged = filtered.slice(start, start + perPage);

        var tbody = document.getElementById('ordersBody');
        var html = '';
        for (var i = 0; i < paged.length; i++) {
            var o = paged[i];
            var statusLabel = o.status.charAt(0).toUpperCase() + o.status.slice(1);
            var actionHtml = '<div style="display:flex;gap:6px;align-items:center;min-width:156px;">';
            if (o.status === 'pending') {
                actionHtml += '<button class="btn btn-sm btn-vendor" style="min-width:72px;" onclick="event.stopPropagation();updateStatus(' + o.id + ', \'processing\')">Accept</button>' +
                              '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="event.stopPropagation();updateStatus(' + o.id + ', \'rejected\')">Reject</button>';
            } else if (o.status === 'processing') {
                actionHtml += '<button class="btn btn-sm btn-vendor" style="min-width:72px;" onclick="event.stopPropagation();updateStatus(' + o.id + ', \'completed\')">Done</button>' +
                              '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="event.stopPropagation();deleteOrder(' + o.id + ', \'' + o.code + '\')">Delete</button>';
            } else {
                actionHtml += '<button class="btn btn-sm btn-danger" style="min-width:72px;" onclick="event.stopPropagation();deleteOrder(' + o.id + ', \'' + o.code + '\')">Delete</button>';
            }
            actionHtml += '</div>';

            html += '<tr onclick="openDetail(' + o.id + ')" style="cursor:pointer;">' +
                '<td><span class="order-id">' + o.code + '</span></td>' +
                '<td>' + o.customer + '</td>' +
                '<td style="color:var(--ctp-subtext0);font-size:0.75rem;">' + o.items + '</td>' +
                '<td style="font-weight:600;">' + o.total + '</td>' +
                '<td style="color:var(--ctp-overlay0);font-size:0.75rem;">' + o.date + '</td>' +
                '<td><span class="badge badge-' + o.status + '">' + statusLabel + '</span></td>' +
                '<td style="min-width:156px;">' + actionHtml + '</td>' +
                '</tr>';
        }
        tbody.innerHTML = html;

        var pagHtml = '';
        if (totalPages > 1) {
            pagHtml += '<button class="btn btn-sm btn-secondary" ' + (currentPage <= 1 ? 'disabled' : '') + ' onclick="goPage(' + (currentPage - 1) + ')">Prev</button>';
            for (var p = 1; p <= totalPages; p++) {
                pagHtml += '<button class="btn btn-sm ' + (p === currentPage ? 'btn-vendor' : 'btn-secondary') + '" onclick="goPage(' + p + ')">' + p + '</button>';
            }
            pagHtml += '<button class="btn btn-sm btn-secondary" ' + (currentPage >= totalPages ? 'disabled' : '') + ' onclick="goPage(' + (currentPage + 1) + ')">Next</button>';
        }
        document.getElementById('pagination').innerHTML = pagHtml;
    }

    function goPage(p) {
        currentPage = p;
        var activeTab = document.querySelector('#statusTabs .tab.active').getAttribute('data-filter');
        renderOrders(activeTab, document.getElementById('searchInput').value);
    }

    function openDetail(orderId) {
        fetch('/vendor/orders/' + orderId + '/detail', {
            headers: { 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); })
        .then(function(o) {
            var statusLabel = o.status.charAt(0).toUpperCase() + o.status.slice(1);
            var itemsHtml = '';
            for (var i = 0; i < o.items.length; i++) {
                var it = o.items[i];
                var lineTotal = it.qty * it.price;
                itemsHtml += '<div class="panel-item-row">' +
                    '<span>' + it.name + ' x' + it.qty + '</span>' +
                    '<span>RM ' + lineTotal.toFixed(2) + '</span>' +
                    '</div>';
            }

            var html = '<div class="panel-section">' +
                '<div class="panel-info-grid">' +
                '<div class="panel-info"><span class="panel-info-label">Order Code</span><span class="panel-info-value">' + o.code + '</span></div>' +
                '<div class="panel-info"><span class="panel-info-label">Status</span><span class="badge badge-' + o.status + '">' + statusLabel + '</span></div>' +
                '<div class="panel-info"><span class="panel-info-label">Customer</span><span class="panel-info-value">' + o.customer + '</span></div>' +
                '<div class="panel-info"><span class="panel-info-label">Location</span><span class="panel-info-value">' + o.location + '</span></div>' +
                '<div class="panel-info"><span class="panel-info-label">Delivery Date</span><span class="panel-info-value">' + o.deliveryDate + '</span></div>' +
                '<div class="panel-info"><span class="panel-info-label">Time Slot</span><span class="panel-info-value">' + o.timeSlot + '</span></div>' +
                '</div></div>';

            html += '<div class="panel-section"><div class="panel-section-title">Items</div>' + itemsHtml +
                '<div class="panel-item-row" style="border-top:1px solid var(--ctp-surface0);padding-top:8px;margin-top:8px;">' +
                '<span>Subtotal</span><span>RM ' + o.subtotal.toFixed(2) + '</span></div>' +
                '<div class="panel-item-row"><span>Delivery Fee</span><span>RM ' + o.deliveryFee.toFixed(2) + '</span></div>' +
                '<div class="panel-item-row total"><span>Total</span><span>RM ' + o.total.toFixed(2) + '</span></div>' +
                '</div>';

            if (o.notes) {
                html += '<div class="panel-section"><div class="panel-section-title">Customer Notes</div>' +
                    '<p style="font-size:0.8rem;color:var(--ctp-subtext0);">' + o.notes + '</p></div>';
            }

            html += '<div class="panel-section"><div class="panel-section-title">Vendor Note</div>' +
                '<textarea id="vendorNoteInput" class="form-textarea" rows="2" placeholder="Add a note for the customer...">' + (o.vendorNote || '') + '</textarea>' +
                '<button class="btn btn-sm btn-vendor" style="margin-top:8px;" onclick="saveVendorNote(' + o.id + ')">Save Note</button></div>';

            var actionsHtml = '<div class="panel-section"><div class="panel-actions">';
            if (o.status === 'pending') {
                actionsHtml += '<button class="btn btn-vendor" onclick="updateStatusAndClose(' + o.id + ', \'processing\')">Accept Order</button>' +
                               '<button class="btn btn-danger" onclick="updateStatusAndClose(' + o.id + ', \'rejected\')">Reject Order</button>';
            } else if (o.status === 'processing') {
                actionsHtml += '<button class="btn btn-vendor" onclick="updateStatusAndClose(' + o.id + ', \'completed\')">Mark Done</button>';
            }
            actionsHtml += '<button class="btn btn-secondary" onclick="printOrder(' + o.id + ')">Print Receipt</button>';
            actionsHtml += '</div></div>';
            html += actionsHtml;

            html += '<div class="panel-section" style="color:var(--ctp-overlay0);font-size:0.7rem;">Created: ' + o.createdAt + '</div>';

            document.getElementById('panelTitle').textContent = o.code;
            document.getElementById('panelBody').innerHTML = html;
            document.getElementById('detailPanel').classList.add('open');
            document.getElementById('panelOverlay').classList.add('open');
        });
    }

    function closePanel() {
        document.getElementById('detailPanel').classList.remove('open');
        document.getElementById('panelOverlay').classList.remove('open');
    }
    document.getElementById('panelClose').addEventListener('click', closePanel);
    document.getElementById('panelOverlay').addEventListener('click', closePanel);

    function saveVendorNote(orderId) {
        var note = document.getElementById('vendorNoteInput').value;
        fetch('/vendor/orders/' + orderId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ vendor_note: note })
        }).then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                for (var i = 0; i < orders.length; i++) {
                    if (orders[i].id === orderId) { orders[i].vendorNote = note; break; }
                }
                showToast('Note saved');
            }
        });
    }

    function updateStatus(orderId, newStatus) {
        fetch('/vendor/orders/' + orderId, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ status: newStatus })
        }).then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                for (var i = 0; i < orders.length; i++) {
                    if (orders[i].id === orderId) { orders[i].status = newStatus; break; }
                }
                var activeTab = document.querySelector('#statusTabs .tab.active').getAttribute('data-filter');
                renderOrders(activeTab, document.getElementById('searchInput').value);
                showToast('Order status updated.');
            }
        });
    }

    function updateStatusAndClose(orderId, newStatus) {
        updateStatus(orderId, newStatus);
        closePanel();
    }

    function deleteOrder(orderId, orderCode) {
        if (!confirm('Delete order ' + orderCode + '?')) return;
        fetch('/vendor/orders/' + orderId, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        }).then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                orders = orders.filter(function(o) { return o.id !== orderId; });
                var activeTab = document.querySelector('#statusTabs .tab.active').getAttribute('data-filter');
                renderOrders(activeTab, document.getElementById('searchInput').value);
                showToast('Order ' + orderCode + ' deleted.');
            }
        });
    }

    function printOrder(orderId) {
        var order = null;
        for (var i = 0; i < orders.length; i++) {
            if (orders[i].id === orderId) { order = orders[i]; break; }
        }
        if (!order) return;

        var printWin = window.open('', '_blank', 'width=400,height=600');
        var itemsHtml = '';
        for (var j = 0; j < order.itemsFull.length; j++) {
            var it = order.itemsFull[j];
            itemsHtml += '<tr><td>' + it.name + '</td><td style="text-align:center;">' + it.qty + '</td><td style="text-align:right;">RM ' + (it.qty * it.price).toFixed(2) + '</td></tr>';
        }
        printWin.document.write('<!DOCTYPE html><html><head><title>Receipt ' + order.code + '</title>' +
            '<style>body{font-family:monospace;font-size:12px;padding:20px;max-width:300px;margin:0 auto;}' +
            'h2{text-align:center;margin:0 0 4px;}p.sub{text-align:center;color:#666;margin:0 0 16px;}' +
            'hr{border:none;border-top:1px dashed #ccc;margin:12px 0;}table{width:100%;border-collapse:collapse;}td{padding:3px 0;}' +
            '.total{font-weight:bold;font-size:14px;}.footer{text-align:center;color:#999;margin-top:16px;font-size:10px;}</style></head><body>' +
            '<h2>{{ Auth::user()->name }}</h2><p class="sub">Order Receipt</p><hr>' +
            '<p><strong>' + order.code + '</strong></p><p>Customer: ' + order.customer + '</p>' +
            '<p>Date: ' + order.deliveryDate + '</p><p>Location: ' + order.location + '</p><hr>' +
            '<table>' + itemsHtml + '</table><hr>' +
            '<table><tr><td>Subtotal</td><td style="text-align:right;">RM ' + order.subtotal.toFixed(2) + '</td></tr>' +
            '<tr><td>Delivery</td><td style="text-align:right;">RM ' + order.deliveryFee.toFixed(2) + '</td></tr>' +
            '<tr class="total"><td>Total</td><td style="text-align:right;">RM ' + order.totalRaw.toFixed(2) + '</td></tr></table><hr>' +
            '<p class="footer">Thank you for your order!</p></body></html>');
        printWin.document.close();
        printWin.focus();
        printWin.print();
    }

    function showToast(msg) {
        var toast = document.createElement('div');
        toast.className = 'toast-msg';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(function() { toast.classList.add('show'); }, 10);
        setTimeout(function() { toast.classList.remove('show'); setTimeout(function() { toast.remove(); }, 300); }, 2000);
    }

    var tabs = document.querySelectorAll('#statusTabs .tab');
    for (var i = 0; i < tabs.length; i++) {
        tabs[i].addEventListener('click', function() {
            for (var j = 0; j < tabs.length; j++) { tabs[j].classList.remove('active'); }
            this.classList.add('active');
            currentPage = 1;
            renderOrders(this.getAttribute('data-filter'), document.getElementById('searchInput').value);
        });
    }

    var rangeTabs = document.querySelectorAll('#rangeTabs .tab');
    for (var i = 0; i < rangeTabs.length; i++) {
        rangeTabs[i].addEventListener('click', function() {
            window.location.href = '/vendor/dashboard?range=' + this.getAttribute('data-range');
        });
    }

    document.getElementById('searchInput').addEventListener('input', function() {
        currentPage = 1;
        var activeTab = document.querySelector('#statusTabs .tab.active').getAttribute('data-filter');
        renderOrders(activeTab, this.value);
    });

    function pollPending() {
        fetch('/vendor/api/stats', { headers: { 'Accept': 'application/json' } })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var alert = document.getElementById('pendingAlert');
            if (data.pending > 0) {
                alert.style.display = 'flex';
                document.getElementById('pendingAlertText').textContent = data.pending + ' pending order' + (data.pending > 1 ? 's' : '');
            } else {
                alert.style.display = 'none';
            }
        }).catch(function() {});
    }
    setInterval(pollPending, 30000);
    pollPending();

    renderOrders('all', '');
</script>
@endsection
