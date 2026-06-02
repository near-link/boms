@extends(Auth::user()->isVendor() ? 'layouts.vendor' : 'layouts.customer', ['title' => 'Help'])

@section('content')
<div class="page-header">
    <h1>Help & Support</h1>
    <p>Frequently asked questions and contact information.</p>
</div>

<div class="card" style="margin-bottom:16px;">
    <div class="card-body">
        <div class="section-label">FAQ</div>

        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                How do I place an order?
                <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
                Browse the menu, add items to your cart, and proceed to checkout. Select your delivery location, date, and time slot, then confirm your order.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                How do I track my order?
                <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
                Go to "My Orders" in the sidebar to see all your orders and their current status. You'll see a timeline showing the progress of each order.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                What payment methods are accepted?
                <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
                Currently we accept Cash on Delivery and Online Payment (mock). Payment is collected at the delivery location.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                How do I manage my products? (Vendors)
                <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
                Navigate to "Products" in the sidebar. From there you can add new products, edit existing ones, adjust pricing and stock levels, and toggle availability.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="this.parentElement.classList.toggle('open')">
                How do I contact support?
                <span class="faq-toggle">+</span>
            </div>
            <div class="faq-answer">
                Email us at support@boms.campus.edu.my or visit the Student Affairs office at Block A, Level 2.
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="section-label">Contact</div>
        <div class="panel-info-grid">
            <div class="panel-info"><span class="panel-info-label">Email</span><span class="panel-info-value">support@boms.campus.edu.my</span></div>
            <div class="panel-info"><span class="panel-info-label">Phone</span><span class="panel-info-value">+60 3-1234 5678</span></div>
            <div class="panel-info"><span class="panel-info-label">Office</span><span class="panel-info-value">Block A, Level 2, Room 201</span></div>
            <div class="panel-info"><span class="panel-info-label">Hours</span><span class="panel-info-value">Mon-Fri, 8:00 AM - 5:00 PM</span></div>
        </div>
    </div>
</div>
@endsection
