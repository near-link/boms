@extends('layouts.vendor', ['title' => 'Delivery Manifest'])

@section('content')
<div class="page-header">
    <h1>Delivery Manifest</h1>
    <p>Orders grouped by time slot and delivery stop for efficient runs.</p>
</div>

@if ($totalOrders === 0)
    <div class="card manifest-empty">
        <div class="card-body" style="text-align:center;padding:48px 24px;">
            <div class="manifest-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ctp-overlay0)" stroke-width="1.5"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            </div>
            <h3 style="margin:16px 0 6px;font-size:1rem;color:var(--ctp-subtext1);">No deliveries pending</h3>
            <p style="font-size:0.8rem;color:var(--ctp-overlay0);max-width:320px;margin:0 auto;">
                Orders with "Processing" status will appear here grouped by delivery stop. Accept pending orders from the dashboard to start.
            </p>
            <a href="{{ route('vendor.dashboard') }}" class="btn btn-vendor" style="margin-top:20px;">Go to Dashboard</a>
        </div>
    </div>
@else
    {{-- Summary bar --}}
    <div class="manifest-summary">
        <div class="manifest-summary-stat">
            <span class="manifest-summary-value">{{ $totalOrders }}</span>
            <span class="manifest-summary-label">Orders</span>
        </div>
        <div class="manifest-summary-stat">
            <span class="manifest-summary-value">{{ $totalStops }}</span>
            <span class="manifest-summary-label">Stops</span>
        </div>
        <div class="manifest-summary-stat">
            <span class="manifest-summary-value">{{ count($manifest) }}</span>
            <span class="manifest-summary-label">Time Slots</span>
        </div>
        <div style="margin-left:auto;">
            <button class="btn btn-vendor" id="bulkCompleteBtn" onclick="bulkComplete()" disabled>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                Mark Delivered (<span id="checkedCount">0</span>)
            </button>
        </div>
    </div>

    @php $globalStop = 1; @endphp
    @foreach ($manifest as $timeSlot => $slotData)
        <div class="manifest-timeslot">
            <div class="manifest-timeslot-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                <span>{{ $timeSlot }}</span>
            </div>

            @foreach ($slotData['stops'] as $location => $stopData)
                @php
                    $stopOrders = $stopData['orders'];
                    $stopOrderCount = count($stopOrders);
                    $stopTotal = array_sum(array_column($stopOrders, 'total'));
                    $stopIds = array_column($stopOrders, 'id');
                @endphp
                <div class="manifest-stop card" data-stop-ids="{{ implode(',', $stopIds) }}">
                    <div class="manifest-stop-header" onclick="toggleStop(this)">
                        <div class="manifest-stop-left">
                            <label class="manifest-checkbox-label" onclick="event.stopPropagation();">
                                <input type="checkbox" class="manifest-checkbox" onchange="updateChecked()" data-ids="{{ implode(',', $stopIds) }}">
                                <span class="manifest-check-custom"></span>
                            </label>
                            <div class="manifest-stop-number">Stop {{ $globalStop }}</div>
                            <div class="manifest-stop-info">
                                <div class="manifest-stop-location">{{ $location }}</div>
                                <div class="manifest-stop-meta">{{ $stopOrderCount }} order{{ $stopOrderCount > 1 ? 's' : '' }} · RM {{ number_format($stopTotal, 2) }}</div>
                            </div>
                        </div>
                        <div class="manifest-stop-toggle">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                    </div>

                    <div class="manifest-stop-body">
                        @foreach ($stopOrders as $order)
                            <div class="manifest-order">
                                <div class="manifest-order-header">
                                    <span class="order-id">{{ $order['code'] }}</span>
                                    <span class="manifest-order-customer">{{ $order['customer'] }}</span>
                                    <span class="manifest-order-total">RM {{ number_format($order['total'], 2) }}</span>
                                </div>
                                <div class="manifest-order-items">
                                    @foreach ($order['items'] as $item)
                                        <span class="manifest-item-tag">{{ $item['name'] }} ×{{ $item['qty'] }}</span>
                                    @endforeach
                                </div>
                                @if ($order['notes'])
                                    <div class="manifest-order-note">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        {{ $order['notes'] }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @php $globalStop++; @endphp
            @endforeach
        </div>
    @endforeach
@endif
@endsection

@section('scripts')
<script>
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function toggleStop(header) {
        var stop = header.closest('.manifest-stop');
        stop.classList.toggle('expanded');
    }

    function updateChecked() {
        var checkboxes = document.querySelectorAll('.manifest-checkbox:checked');
        var count = 0;
        checkboxes.forEach(function(cb) {
            var ids = cb.getAttribute('data-ids').split(',');
            count += ids.length;
        });
        document.getElementById('checkedCount').textContent = count;
        document.getElementById('bulkCompleteBtn').disabled = count === 0;
    }

    function bulkComplete() {
        var checkboxes = document.querySelectorAll('.manifest-checkbox:checked');
        var allIds = [];
        checkboxes.forEach(function(cb) {
            var ids = cb.getAttribute('data-ids').split(',');
            ids.forEach(function(id) { allIds.push(parseInt(id)); });
        });

        if (allIds.length === 0) return;
        if (!confirm('Mark ' + allIds.length + ' order(s) as delivered?')) return;

        fetch('{{ route("vendor.manifest.complete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order_ids: allIds })
        })
        .then(function(r) { return r.json(); })
        .then(function(result) {
            if (result.success) {
                // Remove completed stops from UI
                checkboxes.forEach(function(cb) {
                    var stop = cb.closest('.manifest-stop');
                    stop.style.transition = 'opacity 300ms, transform 300ms';
                    stop.style.opacity = '0';
                    stop.style.transform = 'translateX(20px)';
                    setTimeout(function() { stop.remove(); checkEmptyState(); }, 300);
                });
                showToast(result.updated + ' order(s) delivered!');
                updateChecked();
            }
        })
        .catch(function() {
            showToast('Error completing orders.');
        });
    }

    function checkEmptyState() {
        var stops = document.querySelectorAll('.manifest-stop');
        if (stops.length === 0) {
            setTimeout(function() { location.reload(); }, 500);
        }
    }

    function showToast(msg) {
        var toast = document.createElement('div');
        toast.className = 'toast-msg';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(function() { toast.classList.add('show'); }, 10);
        setTimeout(function() { toast.classList.remove('show'); setTimeout(function() { toast.remove(); }, 300); }, 2000);
    }
</script>
@endsection
