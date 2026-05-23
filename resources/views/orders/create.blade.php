<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="BOMS - Place a new order with campus vendors">
    <title>BOMS - New Order</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="customer-theme">
    <nav class="navbar">
        <div class="navbar-brand">BOMS <span>customer</span></div>
        <ul class="navbar-links">
            <li><a href="{{ route('orders.create') }}" class="active">New Order</a></li>
            <li><a href="{{ route('orders.track.form') }}">Track Order</a></li>
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

    <div class="container" style="max-width:680px;">
        <div class="page-header">
            <h1>Place New Order</h1>
            <p>Submit an order to a campus vendor.</p>
        </div>

        @if ($errors->any())
            <div style="background:rgba(243,139,168,0.12);border:1px solid var(--ctp-red);border-radius:var(--radius-sm);padding:10px 14px;margin-bottom:16px;color:var(--ctp-red);font-size:0.8rem;">
                <ul style="list-style:none;margin:0;padding:0;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="orderForm" method="POST" action="{{ route('orders.store') }}">
            @csrf
            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div class="section-label">Order Details</div>
                    <div class="form-group">
                        <label class="form-label" for="deliveryLocation">Delivery Location</label>
                        <select class="form-select" id="deliveryLocation" name="delivery_location">
                            <option value="" disabled selected>Select your location</option>
                            <option value="blkA">Block A - Main Lobby</option>
                            <option value="blkB">Block B - Cafeteria</option>
                            <option value="lib">Library Entrance</option>
                            <option value="dewan">Dewan Kuliah Utama</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="vendorSelect">Available Vendors</label>
                        <select class="form-select" id="vendorSelect" name="vendor_name" disabled>
                            <option value="" disabled selected>Select a location first</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="orderDate">Delivery Date</label>
                            <input class="form-input" type="date" id="orderDate" name="delivery_date">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="deliveryTime">Time Slot</label>
                            <select class="form-select" id="deliveryTime" name="time_slot">
                                <option value="" disabled selected>Select time</option>
                                <option value="Morning (8:00 - 10:00)">Morning (8:00 - 10:00)</option>
                                <option value="Lunch (12:00 - 14:00)">Lunch (12:00 - 14:00)</option>
                                <option value="Evening (17:00 - 19:00)">Evening (17:00 - 19:00)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:14px;">
                        <div class="section-label" style="margin:0;">Items</div>
                        <button type="button" class="btn btn-secondary btn-sm" id="addItemBtn">+ Add Item</button>
                    </div>
                    <div id="itemsList">
                        <div class="item-row">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Item Name</label>
                                <input class="form-input" type="text" placeholder="e.g. Nasi Lemak Special" name="items[0][name]" data-field="name">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Qty</label>
                                <input class="form-input" type="number" min="1" value="1" name="items[0][qty]" data-field="qty">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Price (RM)</label>
                                <input class="form-input" type="number" step="0.50" min="0" placeholder="0.00" name="items[0][price]" data-field="price">
                            </div>
                            <button type="button" class="btn btn-icon btn-secondary" style="align-self:end;opacity:0.3;pointer-events:none;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                            </button>
                        </div>
                    </div>

                    <div class="order-summary">
                        <div class="summary-row">
                            <span style="color:var(--ctp-subtext0);">Subtotal</span>
                            <span id="subtotal">RM 0.00</span>
                        </div>
                        <div class="summary-row">
                            <span style="color:var(--ctp-subtext0);">Delivery Fee</span>
                            <span>RM 2.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <span style="color:var(--ctp-blue);" id="totalPrice">RM 2.00</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card" style="margin-bottom:16px;">
                <div class="card-body">
                    <div class="section-label">Notes</div>
                    <div class="form-group" style="margin:0;">
                        <textarea class="form-textarea" id="orderNotes" name="notes" placeholder="Special requests or dietary requirements..."></textarea>
                    </div>
                </div>
            </div>

            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">Submit Order</button>
            </div>
        </form>
    </div>

    <script>
        var vendorsByLocation = {
            'blkA': [
                { value: 'Warung Pak Mat', label: 'Warung Pak Mat' },
                { value: 'Kak Yah Catering', label: 'Kak Yah Catering' }
            ],
            'blkB': [
                { value: 'Warung Pak Mat', label: 'Warung Pak Mat' },
                { value: 'Burger Station KL', label: 'Burger Station KL' },
                { value: 'Makcik Cookies', label: 'Makcik Cookies' }
            ],
            'lib': [
                { value: 'Kak Yah Catering', label: 'Kak Yah Catering' },
                { value: 'Makcik Cookies', label: 'Makcik Cookies' }
            ],
            'dewan': [
                { value: 'Warung Pak Mat', label: 'Warung Pak Mat' },
                { value: 'Burger Station KL', label: 'Burger Station KL' }
            ]
        };

        var itemIndex = 1;

        document.getElementById('deliveryLocation').addEventListener('change', function() {
            var vendorSelect = document.getElementById('vendorSelect');
            var vendors = vendorsByLocation[this.value] || [];
            vendorSelect.innerHTML = '<option value="" disabled selected>Choose a vendor</option>';
            for (var i = 0; i < vendors.length; i++) {
                var opt = document.createElement('option');
                opt.value = vendors[i].value;
                opt.textContent = vendors[i].label;
                vendorSelect.appendChild(opt);
            }
            vendorSelect.disabled = false;
        });

        document.getElementById('addItemBtn').addEventListener('click', function() {
            var row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = '<div class="form-group" style="margin:0;">' +
                '<label class="form-label">Item Name</label>' +
                '<input class="form-input" type="text" placeholder="e.g. Teh Tarik" name="items[' + itemIndex + '][name]" data-field="name">' +
                '</div>' +
                '<div class="form-group" style="margin:0;">' +
                '<label class="form-label">Qty</label>' +
                '<input class="form-input" type="number" min="1" value="1" name="items[' + itemIndex + '][qty]" data-field="qty">' +
                '</div>' +
                '<div class="form-group" style="margin:0;">' +
                '<label class="form-label">Price (RM)</label>' +
                '<input class="form-input" type="number" step="0.50" min="0" placeholder="0.00" name="items[' + itemIndex + '][price]" data-field="price">' +
                '</div>' +
                '<button type="button" class="btn btn-icon btn-secondary remove-item" style="align-self:end;">' +
                '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>' +
                '</button>';
            itemIndex++;
            document.getElementById('itemsList').appendChild(row);
            row.querySelector('.remove-item').addEventListener('click', function() {
                row.remove();
                updateTotal();
            });
            attachPriceListeners(row);
        });

        function attachPriceListeners(container) {
            var inputs = container.querySelectorAll('[data-field="qty"], [data-field="price"]');
            for (var i = 0; i < inputs.length; i++) {
                inputs[i].addEventListener('input', updateTotal);
            }
        }

        function updateTotal() {
            var subtotal = 0;
            var rows = document.querySelectorAll('.item-row');
            for (var i = 0; i < rows.length; i++) {
                var qty = parseFloat(rows[i].querySelector('[data-field="qty"]').value) || 0;
                var price = parseFloat(rows[i].querySelector('[data-field="price"]').value) || 0;
                subtotal += qty * price;
            }
            document.getElementById('subtotal').textContent = 'RM ' + subtotal.toFixed(2);
            document.getElementById('totalPrice').textContent = 'RM ' + (subtotal + 2).toFixed(2);
        }

        attachPriceListeners(document);
    </script>
</body>
</html>
