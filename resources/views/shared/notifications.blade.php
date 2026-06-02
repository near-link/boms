@extends(Auth::user()->isVendor() ? 'layouts.vendor' : 'layouts.customer', ['title' => 'Notifications'])

@section('content')
<div class="page-header">
    <h1>Notifications</h1>
    <p>Stay updated on order activity.</p>
</div>

<div class="card" style="text-align:center;padding:40px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ctp-overlay0)" stroke-width="1.5" style="margin-bottom:12px;">
        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
    </svg>
    <p style="color:var(--ctp-overlay0);">No new notifications.</p>
    <p style="color:var(--ctp-overlay0);font-size:0.75rem;">You'll be notified when orders are updated.</p>
</div>
@endsection
