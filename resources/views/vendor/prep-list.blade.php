@extends('layouts.vendor', ['title' => 'Prep List'])

@section('content')
<div class="page-header">
    <h1>Prep List</h1>
    <p>AI-powered preparation recommendations based on historical sales data.</p>
</div>

{{-- Day insight banner --}}
<div class="prep-insight-banner">
    <div class="prep-insight-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
    </div>
    <div class="prep-insight-text">
        <strong>{{ $targetDate->format('l, d M Y') }}</strong>
        <span>— Predictions based on the last {{ $weeksBack }} {{ $targetDayName }}s</span>
    </div>
    <form method="GET" action="{{ route('vendor.prep-list') }}" class="prep-date-form">
        <input type="date" name="date" value="{{ $targetDate->format('Y-m-d') }}" class="form-input prep-date-input" onchange="this.form.submit()">
    </form>
</div>

@if (!$hasData)
    <div class="card prep-empty">
        <div class="card-body" style="text-align:center;padding:48px 24px;">
            <div class="prep-empty-icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ctp-overlay0)" stroke-width="1.5"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>
            </div>
            <h3 style="margin:16px 0 6px;font-size:1rem;color:var(--ctp-subtext1);">Not enough data yet</h3>
            <p style="font-size:0.8rem;color:var(--ctp-overlay0);max-width:360px;margin:0 auto;">
                Predictions need at least one {{ $targetDayName }} of completed orders in the last {{ $weeksBack }} weeks. Keep fulfilling orders and check back soon!
            </p>
        </div>
    </div>
@else
    {{-- Summary strip --}}
    <div class="prep-summary-strip">
        <div class="prep-summary-item">
            <span class="prep-summary-value">{{ count($predictions) }}</span>
            <span class="prep-summary-label">Products</span>
        </div>
        <div class="prep-summary-item">
            <span class="prep-summary-value">{{ $totalItems }}</span>
            <span class="prep-summary-label">Total Portions</span>
        </div>
        <div class="prep-summary-item prep-summary-legend">
            <span class="confidence-dot confidence-high"></span> High
            <span class="confidence-dot confidence-medium"></span> Medium
            <span class="confidence-dot confidence-low"></span> Low
        </div>
    </div>

    {{-- Prep cards --}}
    <div class="prep-grid">
        @foreach ($predictions as $item)
            <div class="card prep-card">
                <div class="prep-card-header">
                    <div>
                        <div class="prep-card-category">{{ $item['category'] }}</div>
                        <div class="prep-card-name">{{ $item['name'] }}</div>
                    </div>
                    <div class="confidence-indicator confidence-{{ $item['confidence'] }}" title="{{ ucfirst($item['confidence']) }} confidence ({{ $item['weeks_data'] }}/{{ $weeksBack }} weeks of data)">
                        <span class="confidence-dot confidence-{{ $item['confidence'] }}"></span>
                        {{ ucfirst($item['confidence']) }}
                    </div>
                </div>

                <div class="prep-card-stats">
                    <div class="prep-stat">
                        <span class="prep-stat-label">Avg Sold</span>
                        <span class="prep-stat-value">{{ $item['avg_qty'] }}</span>
                    </div>
                    <div class="prep-stat prep-stat-highlight">
                        <span class="prep-stat-label">Recommended</span>
                        <span class="prep-stat-value">{{ $item['recommended'] }}</span>
                    </div>
                </div>

                {{-- Mini sparkline chart --}}
                <div class="prep-sparkline">
                    <div class="prep-sparkline-label">Last {{ $weeksBack }} weeks</div>
                    <div class="prep-sparkline-bars">
                        @php
                            $maxWeekly = max($item['weekly_breakdown']) ?: 1;
                        @endphp
                        @foreach ($item['weekly_breakdown'] as $weekIdx => $weekQty)
                            <div class="prep-sparkline-bar-group">
                                <div class="prep-sparkline-bar" style="height: {{ ($weekQty / $maxWeekly) * 100 }}%;"></div>
                                <div class="prep-sparkline-bar-label">W{{ $weekIdx }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
