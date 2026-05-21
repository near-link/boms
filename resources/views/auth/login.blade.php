<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BOMS - Best Order Management System for campus vendors">
    <title>BOMS - Login</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="logo">BOMS</div>
                <p>Best Order Management System</p>
            </div>

            @if ($errors->any())
                <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;text-align:center;">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="login-roles">
                <div class="role-card role-vendor" id="vendorCard">
                    <div class="role-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                    </div>
                    <div class="role-title">Vendor Login</div>
                    <div class="role-desc">Manage incoming orders, update statuses, and track your daily sales.</div>

                    <form method="POST" action="{{ route('login.vendor') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="vendorEmail">Email</label>
                            <input class="form-input" type="email" id="vendorEmail" name="email" placeholder="vendor@campus.edu.my" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="vendorPassword">Password</label>
                            <input class="form-input" type="password" id="vendorPassword" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-vendor btn-block" id="vendorLoginBtn">Sign In as Vendor</button>
                    </form>
                </div>

                <div class="role-card role-customer" id="customerCard">
                    <div class="role-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="role-title">Customer Login</div>
                    <div class="role-desc">Place orders with campus vendors, track deliveries, and manage your account.</div>

                    <form method="POST" action="{{ route('login.customer') }}">
                        @csrf
                        <div class="form-group">
                            <label class="form-label" for="customerEmail">Email</label>
                            <input class="form-input" type="email" id="customerEmail" name="email" placeholder="student@campus.edu.my" value="{{ old('email') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="customerPassword">Password</label>
                            <input class="form-input" type="password" id="customerPassword" name="password" placeholder="Password">
                        </div>
                        <button type="submit" class="btn btn-customer btn-block" id="customerLoginBtn">Sign In as Customer</button>
                    </form>
                </div>
            </div>

            <div class="login-footer">
                Don't have an account? Contact your campus administrator.
            </div>
        </div>
    </div>
</body>
</html>
