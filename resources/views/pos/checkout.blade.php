@extends('layouts.pos')

@push('styles')
<style>
    .checkout-wrap { display: flex; flex: 1; overflow: hidden; gap: 0; }
    .checkout-left  { flex: 0 0 55%; overflow-y: auto; padding: 16px 20px; border-right: 1px solid #eee; }
    .checkout-right { flex: 0 0 45%; overflow-y: auto; padding: 16px 20px; background: #fafafa; }

    .section-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 14px 16px; margin-bottom: 14px; }
    .section-card h5 { font-size: 12px; font-weight: 700; color: #1a1f2e; margin-bottom: 10px; text-transform: uppercase; letter-spacing: .5px; }

    /* Order items */
    .order-item-row { display: flex; align-items: center; gap: 8px; padding: 6px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
    .order-item-row:last-child { border-bottom: none; }
    .order-item-row .item-name  { flex: 1; font-weight: 500; }
    .order-item-row .item-qty   { color: #888; font-size: 12px; white-space: nowrap; }
    .order-item-row .item-price { font-weight: 600; white-space: nowrap; min-width: 80px; text-align: right; }

    /* Totals */
    .totals-row { display: flex; justify-content: space-between; font-size: 13px; padding: 4px 0; color: #555; }
    .totals-row.grand { font-size: 18px; font-weight: 700; color: #1a1f2e; margin-top: 8px; padding-top: 8px; border-top: 2px solid #eee; }

    /* Customer summary */
    .cust-summary-row { display: flex; gap: 6px; font-size: 12px; padding: 3px 0; }
    .cust-summary-label { font-weight: 700; color: #888; min-width: 72px; }
    .cust-summary-val   { color: #1a1f2e; flex: 1; }
    .order-type-badge {
        display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 700;
        background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9;
    }
    .order-type-badge.pickup { background: #e3f2fd; color: #1565c0; border-color: #bbdefb; }
    .order-type-badge.gift   { background: #fff3e0; color: #e65100; border-color: #ffcc80; }

    /* Payment methods */
    .pay-method-group { display: flex; gap: 10px; margin-bottom: 14px; }
    .pay-method-btn {
        flex: 1; padding: 14px 8px; border: 2px solid #ddd; border-radius: 10px;
        background: #fff; cursor: pointer; text-align: center; font-size: 13px; font-weight: 600;
        transition: all .15s; color: #444;
    }
    .pay-method-btn.selected { border-color: #2ecc71; background: #f0fdf4; color: #1a6b3a; }
    .pay-method-btn i { display: block; font-size: 20px; margin-bottom: 5px; color: inherit; }

    /* Form inputs */
    .form-group { margin-bottom: 10px; }
    .form-group label { font-size: 12px; font-weight: 600; color: #555; display: block; margin-bottom: 4px; }
    .form-group input, .form-group textarea, .form-group select {
        width: 100%; border: 1px solid #ddd; border-radius: 6px;
        padding: 7px 10px; font-size: 13px; outline: none; background: #fff;
    }
    .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
        border-color: #2ecc71; box-shadow: 0 0 0 3px rgba(46,204,113,.1);
    }

    /* Change display */
    .change-display { background: #fff3cd; border: 1px solid #ffc107; border-radius: 8px; padding: 10px 14px; font-size: 15px; font-weight: 700; text-align: center; margin-top: 8px; display: none; }
    .change-display.show { display: block; }

    /* Online transfer verification toggle */
    .verify-toggle { display: flex; gap: 8px; margin-top: 4px; }
    .verify-btn {
        flex: 1; padding: 9px 6px; border: 2px solid #ddd; border-radius: 8px;
        background: #f5f5f5; font-size: 12px; font-weight: 600; cursor: pointer; text-align: center;
        transition: all .15s;
    }
    .verify-btn.verified   { border-color: #2ecc71; background: #f0fdf4; color: #1a6b3a; }
    .verify-btn.unverified { border-color: #e67e22; background: #fff8f0; color: #a04000; }

    /* Place order button */
    .btn-place-order {
        width: 100%; background: #2ecc71; color: #fff; border: none;
        border-radius: 10px; padding: 14px; font-size: 17px; font-weight: 700;
        cursor: pointer; margin-top: 14px;
    }
    .btn-place-order:hover { background: #27ae60; }
    .btn-place-order:disabled { background: #95a5a6; cursor: not-allowed; }

    .btn-back { background: none; border: 1px solid #ddd; border-radius: 8px; padding: 8px 16px; font-size: 13px; cursor: pointer; color: #555; }
    .btn-back:hover { background: #f5f5f5; }

    /* Amount to collect banner */
    .collect-banner {
        background: #1a1f2e; color: #fff; border-radius: 10px;
        padding: 12px 16px; text-align: center; margin-bottom: 6px;
    }
    .collect-banner .label { font-size: 11px; opacity: .65; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
    .collect-banner .amount { font-size: 26px; font-weight: 800; color: #2ecc71; }

    /* Gift summary */
    .gift-summary-wrap { background: #fff9f0; border: 1px solid #f0c890; border-radius: 8px; padding: 10px 12px; font-size: 12px; }
    .gift-summary-wrap .gift-hdr { font-weight: 700; color: #e67e22; margin-bottom: 6px; font-size: 12px; text-transform: uppercase; }
</style>
@endpush

@section('content')
<div class="checkout-wrap">

    {{-- ═══ LEFT: Order Summary ═══ --}}
    <div class="checkout-left">
        <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
            <button class="btn-back" onclick="window.location='{{ route('pos.index') }}'">← Back</button>
            <h4 style="font-size:16px; font-weight:700; color:#1a1f2e; margin:0;">POS Checkout</h4>
        </div>

        {{-- Customer / Order Summary --}}
        <div class="section-card" id="customer-summary-card">
            <h5>Customer &amp; Order</h5>
            <div id="customer-summary-body">
                <div class="cust-summary-row">
                    <span class="cust-summary-label">Customer</span>
                    <span class="cust-summary-val" id="s-cust-name">Walk-in</span>
                </div>
                <div class="cust-summary-row" id="s-phone-row" style="display:none;">
                    <span class="cust-summary-label">Phone</span>
                    <span class="cust-summary-val" id="s-cust-phone"></span>
                </div>
                <div class="cust-summary-row">
                    <span class="cust-summary-label">Order Type</span>
                    <span class="cust-summary-val"><span class="order-type-badge pickup" id="s-order-type-badge">Pickup</span></span>
                </div>
                <div class="cust-summary-row" id="s-address-row" style="display:none;">
                    <span class="cust-summary-label">Delivery To</span>
                    <span class="cust-summary-val" id="s-delivery-addr"></span>
                </div>
            </div>
            {{-- Gift summary shown instead for gift orders --}}
            <div id="gift-summary-body" style="display:none;">
                <div class="gift-summary-wrap">
                    <div class="gift-hdr">🎁 Gift Order</div>
                    <div class="cust-summary-row">
                        <span class="cust-summary-label">From</span>
                        <span class="cust-summary-val" id="s-gift-sender"></span>
                    </div>
                    <div class="cust-summary-row">
                        <span class="cust-summary-label">To</span>
                        <span class="cust-summary-val" id="s-gift-receiver"></span>
                    </div>
                    <div class="cust-summary-row">
                        <span class="cust-summary-label">Deliver To</span>
                        <span class="cust-summary-val" id="s-gift-addr"></span>
                    </div>
                    <div id="s-gift-msg-row" class="cust-summary-row" style="display:none;">
                        <span class="cust-summary-label">Message</span>
                        <span class="cust-summary-val" id="s-gift-msg" style="font-style:italic;"></span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Order Items --}}
        <div class="section-card">
            <h5>Order Items</h5>
            @foreach($cartItems as $item)
            <div class="order-item-row">
                <span class="item-name">{{ $item->name }}</span>
                <span class="item-qty">× {{ $item->qty }}</span>
                <span class="item-price">Rs {{ number_format($item->price * $item->qty, 0) }}</span>
            </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="section-card">
            <h5>Order Totals</h5>
            <div class="totals-row"><span>Subtotal</span><span>Rs {{ number_format($subtotal, 2) }}</span></div>
            <div class="totals-row"><span>Tax</span><span>Rs {{ number_format($tax, 2) }}</span></div>
            <div class="totals-row" id="discount-row" style="display:none;">
                <span>Coupon <span id="s-coupon-badge" style="background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9;border-radius:12px;padding:1px 8px;font-size:11px;font-weight:700;"></span></span>
                <span id="discount-display" style="color:#e74c3c;">-Rs 0</span>
            </div>
            <div class="totals-row grand"><span>TOTAL</span><span id="grand-total-display">Rs {{ number_format($total, 2) }}</span></div>
        </div>
    </div>

    {{-- ═══ RIGHT: Payment Form ═══ --}}
    <div class="checkout-right">
        <form action="{{ route('pos.order.place') }}" method="POST" id="checkout-form">
            @csrf

            {{-- Hidden fields populated by JS from sessionStorage --}}
            <input type="hidden" name="discount_amount"  id="f_discount"              value="0">
            <input type="hidden" name="coupon_code"      id="f_coupon_code"           value="">
            <input type="hidden" name="customer_id"      id="f_customer_id"           value="">
            <input type="hidden" name="customer_phone"   id="f_customer_phone"        value="">
            <input type="hidden" name="customer_name"    id="f_customer_name"         value="">
            <input type="hidden" name="order_type"       id="f_order_type"            value="pickup">
            <input type="hidden" name="address_id"       id="f_address_id"            value="">
            <input type="hidden" name="delivery_address" id="f_delivery_address"      value="">
            <input type="hidden" name="delivery_city"    id="f_delivery_city"         value="">
            <input type="hidden" name="save_customer"    id="f_save_customer"         value="0">
            <input type="hidden" name="is_gift"          id="f_is_gift"               value="0">
            <input type="hidden" name="gift_sender_name"     id="f_gift_sender_name"     value="">
            <input type="hidden" name="gift_sender_phone"    id="f_gift_sender_phone"    value="">
            <input type="hidden" name="gift_sender_address"  id="f_gift_sender_address"  value="">
            <input type="hidden" name="gift_sender_city"     id="f_gift_sender_city"     value="">
            <input type="hidden" name="gift_receiver_name"   id="f_gift_receiver_name"   value="">
            <input type="hidden" name="gift_receiver_phone"  id="f_gift_receiver_phone"  value="">
            <input type="hidden" name="gift_receiver_address" id="f_gift_receiver_address" value="">
            <input type="hidden" name="gift_receiver_city"   id="f_gift_receiver_city"   value="">
            <input type="hidden" name="gift_message"     id="f_gift_message"          value="">
            <input type="hidden" name="gift_wrapping"    id="f_gift_wrapping"         value="0">
            <input type="hidden" name="delivery_date"    id="f_delivery_date"         value="">
            <input type="hidden" name="payment_verified" id="f_payment_verified"      value="0">

            @if($errors->any())
            <div style="background:#fde8e8; border: 1px solid #f5c6c6; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:12px; color:#c0392b;">
                @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
            </div>
            @endif

            {{-- Payment Method --}}
            <div class="section-card">
                <h5>Payment Method</h5>

                <div class="pay-method-group">
                    <button type="button" class="pay-method-btn selected" onclick="selectPayment('cash')" id="pm-cash">
                        <i class="icon-money"></i> Cash
                    </button>
                    <button type="button" class="pay-method-btn" onclick="selectPayment('online_transfer')" id="pm-online_transfer">
                        <i class="icon-mobile"></i> Online Transfer
                    </button>
                </div>
                <input type="hidden" name="payment_method" id="payment_method" value="cash">

                {{-- Cash fields --}}
                <div id="cash-fields">
                    <div class="form-group">
                        <label>Cash Received (Rs)</label>
                        <input type="number" name="cash_received" id="cash-received-input" min="0" step="0.01"
                               placeholder="Enter amount received" oninput="calcChange()">
                    </div>
                    <div class="change-display" id="change-display">
                        Change: Rs <span id="change-amount">0.00</span>
                    </div>
                </div>

                {{-- Online Transfer fields --}}
                <div id="online-fields" style="display:none;">
                    <div class="form-group">
                        <label>Platform</label>
                        <select name="online_platform" id="online-platform">
                            <option value="">— Select platform —</option>
                            <option value="JazzCash">JazzCash</option>
                            <option value="EasyPaisa">EasyPaisa</option>
                            <option value="Alfalah Bank">Alfalah Bank</option>
                            <option value="Meezan Bank">Meezan Bank</option>
                            <option value="HBL Bank">HBL Bank</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Transaction ID <span style="color:#888;font-weight:400;">(optional — for later verification)</span></label>
                        <input type="text" name="reference_no" id="reference-no"
                               placeholder="e.g. T20241105123456">
                    </div>
                    <div class="form-group">
                        <label>Payment Status</label>
                        <div class="verify-toggle">
                            <button type="button" class="verify-btn verified" id="vbtn-verified" onclick="setVerified(1)">
                                ✓ Received &amp; Verified
                            </button>
                            <button type="button" class="verify-btn" id="vbtn-pending" onclick="setVerified(0)">
                                ⏳ Pending Verification
                            </button>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Cashier Note <span style="color:#888;font-weight:400;">(optional)</span></label>
                        <textarea name="notes" id="cashier-note" rows="2"
                                  placeholder="e.g. Customer showed screenshot, will verify later…"></textarea>
                    </div>
                </div>
            </div>

            {{-- Order Note --}}
            <div class="section-card">
                <h5>Order Note</h5>
                <div class="form-group" style="margin:0;">
                    <textarea name="order_note" rows="2" placeholder="Optional note for this order…"></textarea>
                </div>
            </div>

            {{-- Amount to collect --}}
            <div class="collect-banner">
                <div class="label">Amount to Collect</div>
                <div class="amount" id="total-to-collect">Rs {{ number_format($total, 2) }}</div>
            </div>

            <button type="submit" class="btn-place-order" id="btn-place">Place Order</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Must be declared before the IIFE so recalcTotal() has the value when it runs.
var baseTotal = parseFloat('{{ $total }}') || 0;

// ─── Restore sessionStorage state ─────────────────────────────────────────────
(function () {
    $('#f_customer_id').val(sessionStorage.getItem('pos_customer_id') || '');
    $('#f_customer_phone').val(sessionStorage.getItem('pos_customer_phone') || '');
    $('#f_customer_name').val(sessionStorage.getItem('pos_customer_name') || '');
    $('#f_order_type').val(sessionStorage.getItem('pos_order_type') || 'pickup');
    $('#f_address_id').val(sessionStorage.getItem('pos_address_id') || '');
    $('#f_delivery_address').val(sessionStorage.getItem('pos_delivery_address') || '');
    $('#f_delivery_city').val(sessionStorage.getItem('pos_delivery_city') || '');
    $('#f_save_customer').val(sessionStorage.getItem('pos_save_customer') || '0');
    $('#f_is_gift').val(sessionStorage.getItem('pos_is_gift') || '0');
    $('#f_gift_sender_name').val(sessionStorage.getItem('pos_gift_sender_name') || '');
    $('#f_gift_sender_phone').val(sessionStorage.getItem('pos_gift_sender_phone') || '');
    $('#f_gift_sender_address').val(sessionStorage.getItem('pos_gift_sender_address') || '');
    $('#f_gift_sender_city').val(sessionStorage.getItem('pos_gift_sender_city') || '');
    $('#f_gift_receiver_name').val(sessionStorage.getItem('pos_gift_receiver_name') || '');
    $('#f_gift_receiver_phone').val(sessionStorage.getItem('pos_gift_receiver_phone') || '');
    $('#f_gift_receiver_address').val(sessionStorage.getItem('pos_gift_receiver_address') || '');
    $('#f_gift_receiver_city').val(sessionStorage.getItem('pos_gift_receiver_city') || '');
    $('#f_gift_message').val(sessionStorage.getItem('pos_gift_message') || '');
    $('#f_gift_wrapping').val(sessionStorage.getItem('pos_gift_wrapping') || '0');

    var couponCode    = sessionStorage.getItem('pos_coupon_code') || '';
    $('#f_coupon_code').val(couponCode);

    buildCustomerSummary();

    // Apply discount and coupon badge
    var savedDiscount = parseFloat(sessionStorage.getItem('pos_discount')) || 0;
    if(couponCode && savedDiscount > 0){
        $('#s-coupon-badge').text(couponCode);
    }
    recalcTotal(savedDiscount);
})();

// ─── Customer / Gift summary (left panel) ─────────────────────────────────────
function buildCustomerSummary() {
    var isGift    = sessionStorage.getItem('pos_is_gift') === '1';
    var custName  = sessionStorage.getItem('pos_customer_name') || '';
    var custPhone = sessionStorage.getItem('pos_customer_phone') || '';
    var orderType = sessionStorage.getItem('pos_order_type') || 'pickup';
    var delivAddr = sessionStorage.getItem('pos_delivery_address') || '';
    var delivCity = sessionStorage.getItem('pos_delivery_city') || '';

    if (isGift) {
        var sName  = sessionStorage.getItem('pos_gift_sender_name') || '';
        var sPhone = sessionStorage.getItem('pos_gift_sender_phone') || '';
        var rName  = sessionStorage.getItem('pos_gift_receiver_name') || '';
        var rPhone = sessionStorage.getItem('pos_gift_receiver_phone') || '';
        var rAddr  = sessionStorage.getItem('pos_gift_receiver_address') || '';
        var rCity  = sessionStorage.getItem('pos_gift_receiver_city') || '';
        var msg    = sessionStorage.getItem('pos_gift_message') || '';

        $('#customer-summary-body').hide();
        $('#gift-summary-body').show();

        $('#s-gift-sender').text(sName + (sPhone ? ' (' + sPhone + ')' : ''));
        $('#s-gift-receiver').text(rName + (rPhone ? ' (' + rPhone + ')' : ''));
        var addrParts = [rAddr, rCity].filter(Boolean);
        $('#s-gift-addr').text(addrParts.join(', ') || '—');
        if (msg) {
            $('#s-gift-msg').text('"' + msg + '"');
            $('#s-gift-msg-row').show();
        }
        return;
    }

    $('#gift-summary-body').hide();
    $('#customer-summary-body').show();

    var displayName = custName || 'Walk-in Customer';
    $('#s-cust-name').text(displayName);

    if (custPhone) {
        $('#s-cust-phone').text(custPhone);
        $('#s-phone-row').show();
    }

    if (orderType === 'booking') {
        $('#s-order-type-badge').text('Booking / Delivery').removeClass('pickup').addClass('booking');
        var addrParts = [delivAddr, delivCity].filter(Boolean);
        if (addrParts.length) {
            $('#s-delivery-addr').text(addrParts.join(', '));
            $('#s-address-row').show();
        }
    } else {
        $('#s-order-type-badge').text('Pickup').addClass('pickup');
    }
}

// ─── Totals ────────────────────────────────────────────────────────────────────
function recalcTotal(disc) {
    disc = parseFloat(disc) || 0;
    var newTotal = Math.max(0, baseTotal - disc);
    $('#f_discount').val(disc);
    $('#grand-total-display').text('Rs ' + newTotal.toLocaleString('en-PK', { minimumFractionDigits: 2 }));
    $('#total-to-collect').text('Rs ' + newTotal.toLocaleString('en-PK', { minimumFractionDigits: 2 }));
    if (disc > 0) {
        $('#discount-display').text('-Rs ' + disc.toLocaleString('en-PK', { minimumFractionDigits: 2 }));
        $('#discount-row').show();
    } else {
        $('#discount-row').hide();
    }
    calcChange();
}

function getCurrentTotal() {
    return Math.max(0, baseTotal - (parseFloat($('#f_discount').val()) || 0));
}

function calcChange() {
    var received = parseFloat($('#cash-received-input').val()) || 0;
    var total    = getCurrentTotal();
    var change   = received - total;
    if (received > 0) {
        $('#change-amount').text(Math.max(0, change).toLocaleString('en-PK', { minimumFractionDigits: 2 }));
        $('#change-display').toggleClass('show', change >= 0);
    } else {
        $('#change-display').removeClass('show');
    }
}

// ─── Payment method ────────────────────────────────────────────────────────────
var onlineVerified = 1; // default: verified when switching to online

function selectPayment(method) {
    $('#payment_method').val(method);
    $('.pay-method-btn').removeClass('selected');
    $('#pm-' + method).addClass('selected');
    $('#cash-fields').toggle(method === 'cash');
    $('#online-fields').toggle(method === 'online_transfer');
    if (method === 'online_transfer') {
        setVerified(1);
    } else {
        $('#f_payment_verified').val('0');
    }
}

function setVerified(val) {
    onlineVerified = val;
    $('#f_payment_verified').val(val ? '1' : '0');
    if (val) {
        $('#vbtn-verified').addClass('verified').removeClass('unverified');
        $('#vbtn-pending').removeClass('verified unverified');
    } else {
        $('#vbtn-pending').addClass('unverified').removeClass('verified');
        $('#vbtn-verified').removeClass('verified unverified');
    }
}

// Clear POS session when order is placed so stale data isn't restored on next visit
$('#checkout-form').on('submit', function(){
    ['pos_customer_id','pos_customer_phone','pos_customer_name',
     'pos_order_type','pos_address_id','pos_delivery_address','pos_delivery_city',
     'pos_save_customer','pos_coupon_code','pos_discount','pos_order_note',
     'pos_is_gift','pos_gift_sender_name','pos_gift_sender_phone',
     'pos_gift_sender_address','pos_gift_sender_city','pos_gift_receiver_name',
     'pos_gift_receiver_phone','pos_gift_receiver_address','pos_gift_receiver_city',
     'pos_gift_message','pos_delivery_date','pos_gift_wrapping'
    ].forEach(function(k){ sessionStorage.removeItem(k); });
});
</script>
@endpush
