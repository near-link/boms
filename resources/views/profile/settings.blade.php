@extends(Auth::user()->isVendor() ? 'layouts.vendor' : 'layouts.customer', ['title' => 'Settings'])

@section('content')
<div class="page-header">
    <h1>Settings</h1>
    <p>Manage your profile and account preferences.</p>
</div>

@if (session('success'))
    <div class="alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
@endif

{{-- Profile Info --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div class="section-label">Profile Information</div>
        <form method="POST" action="{{ Auth::user()->isVendor() ? route('vendor.settings.update') : route('customer.settings.update') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-input" value="{{ old('name', $user->name) }}" required>
                @error('name')<span style="color:var(--ctp-red);font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" value="{{ old('email', $user->email) }}" required>
                    @error('email')<span style="color:var(--ctp-red);font-size:0.75rem;">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-input" value="{{ old('phone', $user->phone) }}" placeholder="Optional">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-input" value="{{ old('address', $user->address) }}" placeholder="Optional">
            </div>
            <button type="submit" class="btn {{ Auth::user()->isVendor() ? 'btn-vendor' : 'btn-customer' }}">Update Profile</button>
        </form>
    </div>
</div>

{{-- Change Password --}}
<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div class="section-label">Change Password</div>
        <form method="POST" action="{{ Auth::user()->isVendor() ? route('vendor.settings.password') : route('customer.settings.password') }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-input" required>
                @error('current_password')<span style="color:var(--ctp-red);font-size:0.75rem;">{{ $message }}</span>@enderror
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="form-input" required>
                </div>
            </div>
            @error('password')<span style="color:var(--ctp-red);font-size:0.75rem;">{{ $message }}</span>@enderror
            <button type="submit" class="btn btn-secondary">Change Password</button>
        </form>
    </div>
</div>

{{-- Delete Account --}}
<div class="card" style="border-color:var(--ctp-red);">
    <div class="card-body">
        <div class="section-label" style="color:var(--ctp-red);">Danger Zone</div>
        <p style="font-size:0.8rem;color:var(--ctp-subtext0);margin-bottom:12px;">Permanently delete your account. This action cannot be undone.</p>
        <form method="POST" action="{{ Auth::user()->isVendor() ? route('vendor.settings.destroy') : route('customer.settings.destroy') }}" onsubmit="return confirm('Are you sure? This will permanently delete your account.')">
            @csrf @method('DELETE')
            <div class="form-group">
                <label class="form-label">Type DELETE to confirm</label>
                <input type="text" name="delete_confirmation" class="form-input" placeholder="DELETE" required style="max-width:200px;">
            </div>
            <button type="submit" class="btn btn-danger">Delete Account</button>
        </form>
    </div>
</div>
@endsection
