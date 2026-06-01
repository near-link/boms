<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BOMS - Create a manual order">
    <title>BOMS - New Order</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="vendor-theme">
    <nav class="navbar">
        <div class="navbar-brand">BOMS <span>vendor</span></div>
        <div class="navbar-title">New Order</div>
        <div class="navbar-user">
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-secondary">Back to Dashboard</a>
            <span class="navbar-role-tag">Vendor</span>
            <span>{{ Auth::user()->name }}</span>
            <div class="navbar-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) . strtoupper(substr(explode(' ', Auth::user()->name)[1] ?? '', 0, 1)) }}</div>
        </div>
    </nav>

    <div class="container" style="max-width:720px;">
        <div class="page-header">
            <h1>Create Manual Order</h1>
            <p>Add an order for a walk-in or phone customer.</p>
        </div>

        @if ($errors->any())
            <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.orders.store') }}" id="orderForm">
            @csrf

            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Customer Name</label>
                        <input type="text" name="customer_name" class="form-input" placeholder="e.g. Ahmad (walk-in)" required value="{{ old('customer_name') }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Delivery Location</label>
                            <select name="delivery_location" class="form-select" required>
                                <option value="">Select location</option>
                                <option value="Block A - Main Lobby">Block A - Main Lobby</option>
                                <option value="Block B - Cafeteria">Block B - Cafeteria</option>
                                <option value="Library Entrance">Library Entrance</option>
                                <option value="Dewan Kuliah Utama">Dewan Kuliah Utama</option>
                                <option value="Counter">Counter (walk-in)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Time Slot</label>
                            <select name="time_slot" class="form-select" required>
                                <option value="">Select time</option>
                                <option value="Morning (8:00 - 10:00)">Morning (8:00 - 10:00)</option>
                                <option value="Lunch (12:00 - 14:00)">Lunch (12:00 - 14:00)</option>
                                <option value="Evening (17:00 - 19:00)">Evening (17:00 - 19:00)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Notes (optional)</label>
                        <textarea name="notes" class="form-textarea" rows="2" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
                        <span style="font-weight:600;font-size:0.85rem;">Order Items</span>
                        <button type="button" class="btn btn-sm btn-vendor" onclick="addItem()">+ Add Item</button>
                    </div>

                    <div id="itemsContainer">
                        <div class="item-row" data-index="0">
                            <div class="form-group" style="margin-bottom:0;">
                                <input type="text" name="items[0][name]" class="form-input" placeholder="Item name" required>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <input type="number" name="items[0][qty]" class="form-input" placeholder="Qty" min="1" value="1" required oninput="calcTotal()">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <input type="number" name="items[0][price]" class="form-input" placeholder="Price (RM)" min="0" step="0.01" required oninput="calcTotal()">
                            </div>
                            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)" style="align-self:center;">x</button>
                        </div>
                    </div>

                    <div class="order-summary">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="subtotalDisplay">RM 0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>RM 2.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span id="totalDisplay">RM 2.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-vendor btn-block">Create Order</button>
        </form>
    </div>

    <script>
        var itemIndex = 1;

        function addItem() {
            var container = document.getElementById('itemsContainer');
            var html = '<div class="item-row" data-index="' + itemIndex + '">' +
                '<div class="form-group" style="margin-bottom:0;"><input type="text" name="items[' + itemIndex + '][name]" class="form-input" placeholder="Item name" required></div>' +
                '<div class="form-group" style="margin-bottom:0;"><input type="number" name="items[' + itemIndex + '][qty]" class="form-input" placeholder="Qty" min="1" value="1" required oninput="calcTotal()"></div>' +
                '<div class="form-group" style="margin-bottom:0;"><input type="number" name="items[' + itemIndex + '][price]" class="form-input" placeholder="Price (RM)" min="0" step="0.01" required oninput="calcTotal()"></div>' +
                '<button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)" style="align-self:center;">x</button>' +
                '</div>';
            container.insertAdjacentHTML('beforeend', html);
            itemIndex++;
        }

        function removeItem(btn) {
            var rows = document.querySelectorAll('.item-row');
            if (rows.length <= 1) return;
            btn.closest('.item-row').remove();
            calcTotal();
        }

        function calcTotal() {
            var rows = document.querySelectorAll('.item-row');
            var subtotal = 0;
            rows.forEach(function(row) {
                var qty = parseFloat(row.querySelector('input[name*="[qty]"]').value) || 0;
                var price = parseFloat(row.querySelector('input[name*="[price]"]').value) || 0;
                subtotal += qty * price;
            });
            document.getElementById('subtotalDisplay').textContent = 'RM ' + subtotal.toFixed(2);
            document.getElementById('totalDisplay').textContent = 'RM ' + (subtotal + 2).toFixed(2);
        }
    </script>
</body>
</html>
