<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BOMS - Track your campus order status in real time">
    <title>BOMS - Track Order</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="customer-theme">
    <nav class="navbar">
        <div class="navbar-brand">BOMS <span>customer</span></div>
        <ul class="navbar-links">
            <li><a href="{{ route('orders.create') }}">New Order</a></li>
            <li><a href="{{ route('orders.track.form') }}" class="active">Track Order</a></li>
            <li><a href="{{ route('profile.show') }}">Profile</a></li>
        </ul>
        <div class="navbar-user">
            <span class="navbar-role-tag">Customer</span>
            <span>{{ Auth::user()->name }}</span>
            <div class="navbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) . strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm" style="padding:4px 10px;font-size:0.65rem;">Logout</button>
            </form>
        </div>
    </nav>

    <div class="tracking-hero">
        <h1>Track Your Order</h1>
        <p>Enter your order ID to check the real-time status.</p>
        <form method="POST" action="{{ route('orders.search') }}" class="tracking-search">
            @csrf
            <input type="text" id="trackInput" name="order_code" placeholder="e.g. BOM-2401" value="{{ $order->order_code ?? '' }}">
            <button type="submit" class="btn btn-primary" id="trackBtn">Track</button>
        </form>
    </div>

    @if (session('success'))
        <div style="max-width:600px;margin:0 auto;padding:0 20px;">
            <div class="alert-success">{{ session('success') }}</div>
        </div>
    @endif

    @if ($order)
    <div class="tracking-result" id="trackingResult">
        <div class="card" style="margin-bottom:16px;">
            <div class="card-body">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                    <div>
                        <span class="order-id" style="font-size:1.1rem;" id="resultOrderId">{{ $order->order_code }}</span>
                        <p style="color:var(--ctp-overlay0);font-size:0.75rem;margin-top:2px;">{{ $order->created_at->format('F d, Y') }}</p>
                    </div>
                    <span class="badge badge-{{ $order->status }}" id="resultBadge">{{ ucfirst($order->status) }}</span>
                </div>

                @if ($order->status === 'rejected')
                    {{-- Rejected state --}}
                    <div style="background:rgba(243,139,168,0.1);border:1px solid rgba(243,139,168,0.3);border-radius:var(--radius-sm);padding:16px;margin-bottom:20px;">
                        <div style="font-weight:600;color:var(--ctp-red);margin-bottom:4px;">Order Rejected</div>
                        <p style="font-size:0.8rem;color:var(--ctp-subtext0);">This order was declined by the vendor. Please contact them or place a new order.</p>
                        @if ($order->vendor_note)
                            <p style="font-size:0.8rem;color:var(--ctp-text);margin-top:8px;"><strong>Vendor note:</strong> {{ $order->vendor_note }}</p>
                        @endif
                    </div>
                @else
                    @php
                        $statusSteps = ['pending' => 1, 'processing' => 3, 'completed' => 5];
                        $currentStep = $statusSteps[$order->status] ?? 1;
                        $progressPercent = match($order->status) {
                            'pending' => '10%',
                            'processing' => '40%',
                            'completed' => '100%',
                            default => '0%',
                        };
                    @endphp

                    <div class="progress-steps">
                        <div class="progress-fill" style="width:{{ $progressPercent }};"></div>
                        <div class="progress-step">
                            <div class="step-circle {{ $currentStep >= 1 ? 'done' : '' }}">1</div>
                            <span class="step-label {{ $currentStep >= 1 ? 'done' : '' }}">Placed</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-circle {{ $currentStep >= 2 ? 'done' : ($currentStep >= 1 ? 'active' : '') }}">2</div>
                            <span class="step-label {{ $currentStep >= 2 ? 'done' : ($currentStep >= 1 ? 'active' : '') }}">Confirmed</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-circle {{ $currentStep >= 4 ? 'done' : ($currentStep >= 3 ? 'active' : '') }}">3</div>
                            <span class="step-label {{ $currentStep >= 4 ? 'done' : ($currentStep >= 3 ? 'active' : '') }}">Preparing</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-circle {{ $currentStep >= 5 ? 'done' : ($currentStep >= 4 ? 'active' : '') }}">4</div>
                            <span class="step-label {{ $currentStep >= 5 ? 'done' : ($currentStep >= 4 ? 'active' : '') }}">Ready</span>
                        </div>
                        <div class="progress-step">
                            <div class="step-circle {{ $currentStep >= 5 ? 'done' : '' }}">5</div>
                            <span class="step-label {{ $currentStep >= 5 ? 'done' : '' }}">Delivered</span>
                        </div>
                    </div>
                @endif

                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-label">Vendor</div>
                        <div class="detail-value">{{ $order->vendor_name }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Location</div>
                        <div class="detail-value">{{ $order->delivery_location }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Time Slot</div>
                        <div class="detail-value">{{ $order->time_slot }}</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Total</div>
                        <div class="detail-value" style="color:var(--ctp-blue);">RM {{ number_format($order->total, 2) }}</div>
                    </div>
                </div>

                @if ($order->vendor_note && $order->status !== 'rejected')
                    <div style="background:rgba(203,166,247,0.1);border:1px solid rgba(203,166,247,0.2);border-radius:var(--radius-sm);padding:12px;margin-bottom:16px;">
                        <div style="font-size:0.7rem;font-weight:600;color:var(--ctp-mauve);margin-bottom:4px;">Note from Vendor</div>
                        <p style="font-size:0.8rem;color:var(--ctp-text);">{{ $order->vendor_note }}</p>
                    </div>
                @endif

                <div class="section-label">Timeline</div>
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-dot done"></div>
                        <div class="timeline-title">Order Placed</div>
                        <div class="timeline-time">{{ $order->created_at->format('g:i A') }} - Submitted by customer</div>
                    </div>
                    @if ($order->status === 'rejected')
                        <div class="timeline-item">
                            <div class="timeline-dot" style="border-color:var(--ctp-red);background:var(--ctp-red);"></div>
                            <div class="timeline-title" style="color:var(--ctp-red);">Order Rejected</div>
                            <div class="timeline-time">{{ $order->updated_at->format('g:i A') }} - Declined by vendor</div>
                        </div>
                    @else
                        @php $currentStep = $statusSteps[$order->status] ?? 1; @endphp
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $currentStep >= 2 ? 'done' : ($currentStep >= 1 ? 'active' : '') }}"></div>
                            <div class="timeline-title" {!! $currentStep < 2 ? 'style="color:var(--ctp-overlay0);"' : '' !!}>Order Confirmed</div>
                            <div class="timeline-time">{{ $currentStep >= 2 ? $order->updated_at->format('g:i A') . ' - Accepted by vendor' : 'Pending' }}</div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $currentStep >= 4 ? 'done' : ($currentStep >= 3 ? 'active' : '') }}"></div>
                            <div class="timeline-title" {!! $currentStep < 3 ? 'style="color:var(--ctp-overlay0);"' : '' !!}>Preparing</div>
                            <div class="timeline-time">{{ $currentStep >= 3 ? $order->updated_at->format('g:i A') . ' - Vendor is preparing items' : 'Pending' }}</div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $currentStep >= 5 ? 'done' : ($currentStep >= 4 ? 'active' : '') }}"></div>
                            <div class="timeline-title" {!! $currentStep < 4 ? 'style="color:var(--ctp-overlay0);"' : '' !!}>Ready for Pickup</div>
                            <div class="timeline-time">{{ $currentStep >= 4 ? 'Ready' : 'Pending' }}</div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-dot {{ $currentStep >= 5 ? 'done' : '' }}"></div>
                            <div class="timeline-title" {!! $currentStep < 5 ? 'style="color:var(--ctp-overlay0);"' : '' !!}>Delivered</div>
                            <div class="timeline-time">{{ $currentStep >= 5 ? $order->updated_at->format('g:i A') . ' - Completed' : 'Pending' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="section-label">Items</div>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th style="text-align:right;">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                        <tr>
                            <td>{{ $item['name'] }}</td>
                            <td>{{ $item['qty'] }}</td>
                            <td style="text-align:right;font-weight:600;">RM {{ number_format($item['qty'] * $item['price'], 2) }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td style="color:var(--ctp-overlay0);font-size:0.75rem;">Delivery Fee</td>
                            <td></td>
                            <td style="text-align:right;color:var(--ctp-overlay0);">RM {{ number_format($order->delivery_fee, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
                <div style="display:flex;justify-content:space-between;padding:12px 0 0;border-top:1px solid var(--ctp-surface0);margin-top:10px;font-weight:700;">
                    <span>Total</span>
                    <span style="color:var(--ctp-blue);">RM {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
    @elseif (request()->isMethod('post') || request()->route('orderCode'))
        <div style="max-width:600px;margin:0 auto;padding:24px 20px;text-align:center;">
            <div class="card">
                <div class="card-body" style="padding:32px;">
                    <p style="color:var(--ctp-overlay0);">No order found. Please check the order ID and try again.</p>
                </div>
            </div>
        </div>
    @endif
</body>
</html>
