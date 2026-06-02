@extends('layouts.vendor', ['title' => 'New Manual Order'])

@section('content')
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

{{-- AI WhatsApp Parser Card --}}
<div class="card magic-paste-card" style="margin-bottom:16px;">
    <div class="card-body">
        <div class="magic-paste-header">
            <div class="magic-paste-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            </div>
            <div>
                <div class="magic-paste-title">Magic Paste <span class="magic-paste-badge">AI ✨</span></div>
                <div class="magic-paste-desc">Paste a WhatsApp message and let AI parse it into an order.</div>
            </div>
        </div>

        <textarea id="whatsappMessage" class="form-textarea magic-paste-textarea" rows="3" placeholder='e.g. "Hi, nak order 2 nasi ayam hantar gi block B pukul 1 petang"'></textarea>

        <div class="magic-paste-actions">
            <button type="button" class="btn btn-vendor magic-parse-btn" id="parseBtn" onclick="parseMessage()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                <span>Parse with AI</span>
            </button>
            <div class="magic-paste-spinner" id="parseSpinner" style="display:none;">
                <div class="spinner"></div>
                <span>Analyzing message...</span>
            </div>
        </div>

        <div class="magic-paste-error" id="parseError" style="display:none;"></div>
        <div class="magic-paste-success" id="parseSuccess" style="display:none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span>Order parsed successfully! Review the form below.</span>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('vendor.orders.store') }}" id="orderForm">
    @csrf

    <div class="card" style="margin-bottom:16px;">
        <div class="card-body">
            <div class="form-group">
                <label class="form-label">Customer Name</label>
                <input type="text" name="customer_name" id="customerNameInput" class="form-input" placeholder="e.g. Ahmad (walk-in)" required value="{{ old('customer_name') }}">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Delivery Location</label>
                    <select name="delivery_location" id="deliveryLocationInput" class="form-select" required>
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
                    <select name="time_slot" id="timeSlotInput" class="form-select" required>
                        <option value="">Select time</option>
                        <option value="Morning (8:00 - 10:00)">Morning (8:00 - 10:00)</option>
                        <option value="Lunch (12:00 - 14:00)">Lunch (12:00 - 14:00)</option>
                        <option value="Evening (17:00 - 19:00)">Evening (17:00 - 19:00)</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Notes (optional)</label>
                <textarea name="notes" id="notesInput" class="form-textarea" rows="2" placeholder="Any special instructions...">{{ old('notes') }}</textarea>
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
@endsection

@section('scripts')
<script>
    var itemIndex = 1;
    var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function parseMessage() {
        var message = document.getElementById('whatsappMessage').value.trim();
        if (!message) {
            showParseError('Please paste a WhatsApp message first.');
            return;
        }
        if (message.length < 5) {
            showParseError('Message is too short to parse.');
            return;
        }

        // Show spinner, hide other states
        document.getElementById('parseBtn').style.display = 'none';
        document.getElementById('parseSpinner').style.display = 'flex';
        document.getElementById('parseError').style.display = 'none';
        document.getElementById('parseSuccess').style.display = 'none';

        fetch('{{ route("vendor.orders.ai-parse") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        })
        .then(function(r) { return r.json(); })
        .then(function(result) {
            document.getElementById('parseSpinner').style.display = 'none';
            document.getElementById('parseBtn').style.display = 'inline-flex';

            if (!result.success) {
                showParseError(result.error || 'Failed to parse message.');
                return;
            }

            var data = result.data;

            // Populate customer name
            if (data.customer_name) {
                document.getElementById('customerNameInput').value = data.customer_name;
            }

            // Populate delivery location
            if (data.delivery_location) {
                var locSelect = document.getElementById('deliveryLocationInput');
                for (var i = 0; i < locSelect.options.length; i++) {
                    if (locSelect.options[i].value === data.delivery_location) {
                        locSelect.selectedIndex = i;
                        break;
                    }
                }
            }

            // Populate time slot
            if (data.time_slot) {
                var tsSelect = document.getElementById('timeSlotInput');
                for (var i = 0; i < tsSelect.options.length; i++) {
                    if (tsSelect.options[i].value === data.time_slot) {
                        tsSelect.selectedIndex = i;
                        break;
                    }
                }
            }

            // Populate notes
            if (data.notes) {
                document.getElementById('notesInput').value = data.notes;
            }

            // Populate items
            if (data.items && data.items.length > 0) {
                // Clear existing items
                document.getElementById('itemsContainer').innerHTML = '';
                itemIndex = 0;

                for (var j = 0; j < data.items.length; j++) {
                    var item = data.items[j];
                    addItemWithData(item.name || '', item.qty || 1, item.price || 0);
                }
                calcTotal();
            }

            document.getElementById('parseSuccess').style.display = 'flex';

            // Smooth scroll to the form
            document.getElementById('orderForm').scrollIntoView({ behavior: 'smooth', block: 'start' });
        })
        .catch(function(err) {
            document.getElementById('parseSpinner').style.display = 'none';
            document.getElementById('parseBtn').style.display = 'inline-flex';
            showParseError('Network error. Please try again.');
        });
    }

    function showParseError(msg) {
        var el = document.getElementById('parseError');
        el.textContent = msg;
        el.style.display = 'block';
        document.getElementById('parseSuccess').style.display = 'none';
    }

    function addItemWithData(name, qty, price) {
        var container = document.getElementById('itemsContainer');
        var html = '<div class="item-row" data-index="' + itemIndex + '">' +
            '<div class="form-group" style="margin-bottom:0;"><input type="text" name="items[' + itemIndex + '][name]" class="form-input" placeholder="Item name" required value="' + escapeHtml(name) + '"></div>' +
            '<div class="form-group" style="margin-bottom:0;"><input type="number" name="items[' + itemIndex + '][qty]" class="form-input" placeholder="Qty" min="1" value="' + qty + '" required oninput="calcTotal()"></div>' +
            '<div class="form-group" style="margin-bottom:0;"><input type="number" name="items[' + itemIndex + '][price]" class="form-input" placeholder="Price (RM)" min="0" step="0.01" value="' + parseFloat(price).toFixed(2) + '" required oninput="calcTotal()"></div>' +
            '<button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)" style="align-self:center;">x</button>' +
            '</div>';
        container.insertAdjacentHTML('beforeend', html);
        itemIndex++;
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.appendChild(document.createTextNode(text));
        return div.innerHTML;
    }

    function addItem() {
        addItemWithData('', 1, '');
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
@endsection
