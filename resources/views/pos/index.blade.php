@extends('layouts.pos')

@section('content')

<div class="pos-main">

    {{-- ── Left Panel: Products ── --}}
    <div class="pos-left">
        {{-- Search bar --}}
        <div class="pos-search-bar">
            <input type="text" id="pos-search" placeholder="🔍  Search product name, SKU or scan barcode…" autofocus autocomplete="off">
            <button class="btn-hold" style="font-size:12px; padding:6px 12px;" onclick="window.location='{{ route('pos.held') }}'">
                Held <span class="held-badge" id="held-badge">{{ $heldCount }}</span>
            </button>
        </div>

        {{-- Category filters --}}
        <div class="pos-categories">
            <button class="cat-btn active" data-cat="">All</button>
            @foreach($categories as $cat)
                <button class="cat-btn" data-cat="{{ $cat->id }}">{{ $cat->name }}</button>
            @endforeach
        </div>

        {{-- Products grid --}}
        <div class="pos-products-grid" id="pos-products-grid">
            <div style="grid-column:1/-1; text-align:center; padding:60px; color:#aaa;">
                Start typing to search for products…
            </div>
        </div>
    </div>

    {{-- ── Right Panel: Order ── --}}
    <div class="pos-right">

        {{-- Customer Section --}}
        <div class="pos-customer">

            {{-- Step 1: Phone --}}
            <div style="position:relative;">
                <input type="text" id="cust-phone" placeholder="📱 Phone (03XXXXXXXXX)" autocomplete="off"
                       inputmode="tel" style="width:100%;border:2px solid #ddd;border-radius:8px;padding:7px 32px 7px 10px;font-size:13px;font-weight:500;outline:none;">
                <button id="cust-clear-btn" onclick="clearCustomer()"
                        style="display:none;position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;color:#aaa;font-size:15px;cursor:pointer;line-height:1;padding:0;">✕</button>
            </div>
            <div id="cust-status" style="min-height:14px;font-size:11px;color:#aaa;margin:3px 0 2px;"></div>

            {{-- Found: existing customer --}}
            <div id="cust-found-panel" style="display:none;">
                <div class="cust-chip cust-chip-found">✓ <span id="cust-found-name"></span></div>

                <div class="ot-toggle">
                    <button class="ot-btn active" id="ot-found-pickup" onclick="setOrderType('pickup')">🏪 Pickup</button>
                    <button class="ot-btn" id="ot-found-delivery" onclick="setOrderType('booking')">🚚 Delivery</button>
                </div>

                <div id="found-delivery-wrap" style="display:none;">
                    <div class="cust-section-label">Delivery Address</div>
                    <div id="cust-addr-list"></div>
                    <button class="btn-add-addr" onclick="showAddressForm()">+ Add New Address</button>
                    <div id="cust-addr-form" class="addr-form-wrap" style="display:none;">
                        <input type="hidden" id="edit-addr-id" value="">
                        <div style="display:flex;gap:4px;margin-bottom:4px;">
                            <select id="addr-form-title" style="flex:0 0 90px;border:1px solid #ddd;border-radius:6px;padding:5px 6px;font-size:12px;">
                                <option>Home</option><option>Office</option><option>Other</option>
                            </select>
                            <label style="display:flex;align-items:center;gap:4px;font-size:11px;flex:1;cursor:pointer;">
                                <input type="checkbox" id="addr-form-default" style="width:13px;height:13px;"> Default
                            </label>
                        </div>
                        <input type="text" id="addr-form-address" placeholder="Full address *"
                               style="width:100%;border:1px solid #ddd;border-radius:6px;padding:5px 8px;font-size:12px;margin-bottom:4px;">
                        <input type="text" id="addr-form-city" list="pak-cities" placeholder="City *" autocomplete="off"
                               style="width:100%;border:1px solid #ddd;border-radius:6px;padding:5px 8px;font-size:12px;margin-bottom:6px;">
                        <div style="display:flex;gap:4px;">
                            <button onclick="saveAddress()" style="flex:1;background:#2ecc71;color:#fff;border:none;border-radius:6px;padding:5px 0;font-size:12px;font-weight:600;cursor:pointer;">Save</button>
                            <button onclick="cancelAddressForm()" style="flex:1;background:#eee;color:#555;border:none;border-radius:6px;padding:5px 0;font-size:12px;cursor:pointer;">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- New: no customer found --}}
            <div id="cust-new-panel" style="display:none;">
                <div class="cust-chip cust-chip-new">⚡ New Customer</div>
                <input type="text" id="cust-new-name" class="cust-mini-input" placeholder="Full Name *" autocomplete="off">

                <div class="ot-toggle">
                    <button class="ot-btn active" id="ot-new-pickup" onclick="setOrderType('pickup')">🏪 Pickup</button>
                    <button class="ot-btn" id="ot-new-booking" onclick="setOrderType('booking')">🚚 Booking</button>
                </div>

                <div id="new-booking-wrap" style="display:none;">
                    <input type="text" id="cust-new-address" class="cust-mini-input" placeholder="Full delivery address *" autocomplete="off">
                    <input type="text" id="cust-new-city" list="pak-cities" class="cust-mini-input" placeholder="Delivery city *" autocomplete="off">
                </div>
            </div>

        </div>

        {{-- Gift Toggle --}}
        <div class="gift-toggle-bar">
            <label>
                <input type="checkbox" id="gift-toggle" onchange="toggleGift(this.checked)">
                🎁 This is a Gift Order
            </label>
        </div>

        {{-- Gift Details Form (hidden by default) --}}
        <div id="gift-panel">

            <h6>Sender Information</h6>
            <div class="gift-row">
                <input type="text" id="gift-sender-name" placeholder="Sender Name *" autocomplete="off">
                <input type="text" id="gift-sender-phone" placeholder="Sender Phone * (03XXXXXXXXX)" autocomplete="off">
            </div>
            <input type="text" id="gift-sender-address" placeholder="Sender Address *" autocomplete="off">
            <input type="text" id="gift-sender-city" list="pak-cities" placeholder="Sender City — type to search *" autocomplete="off">

            <h6>Receiver Information</h6>
            <div class="gift-row">
                <input type="text" id="gift-receiver-name" placeholder="Receiver Name *" autocomplete="off">
                <input type="text" id="gift-receiver-phone" placeholder="Receiver Phone * (03XXXXXXXXX)" autocomplete="off">
            </div>
            <div id="gift-receiver-status" style="display:none;font-size:11px;margin:2px 0 4px;"></div>

            <h6>Delivery Information</h6>
            <input type="text" id="gift-address" placeholder="Full Delivery Address *" autocomplete="off">
            <input type="text" id="gift-receiver-city" list="pak-cities" placeholder="Receiver City — type to search *" autocomplete="off">

            <h6>Gift Message</h6>
            <textarea id="gift-message" rows="2" placeholder="Write a message for the recipient… (optional)" style="resize:none;"></textarea>
            <div style="display:flex;align-items:center;gap:8px;font-size:12px;margin-bottom:4px;">
                <input type="checkbox" id="gift-wrapping" style="width:14px;height:14px;margin-bottom:0;">
                <label for="gift-wrapping">Gift wrapping requested</label>
            </div>

        </div>

        {{-- Pakistan cities datalist — shared by sender & receiver city inputs --}}
        <datalist id="pak-cities">
            <option value="Karachi"><option value="Lahore"><option value="Islamabad">
            <option value="Rawalpindi"><option value="Faisalabad"><option value="Multan">
            <option value="Peshawar"><option value="Quetta"><option value="Sialkot">
            <option value="Gujranwala"><option value="Hyderabad"><option value="Bahawalpur">
            <option value="Sargodha"><option value="Sukkur"><option value="Abbottabad">
            <option value="Dera Ghazi Khan"><option value="Sheikhupura"><option value="Jhang">
            <option value="Rahim Yar Khan"><option value="Gujrat"><option value="Sahiwal">
            <option value="Wah Cantonment"><option value="Mardan"><option value="Kasur">
            <option value="Dera Ismail Khan"><option value="Nawabshah"><option value="Mingora">
            <option value="Chiniot"><option value="Okara"><option value="Mandi Bahauddin">
            <option value="Jhelum"><option value="Sadiqabad"><option value="Khanewal">
            <option value="Mirpur Khas"><option value="Larkana"><option value="Muzaffarabad">
            <option value="Turbat"><option value="Kohat"><option value="Jacobabad">
            <option value="Shikarpur"><option value="Khushab"><option value="Nowshera">
            <option value="Hafizabad"><option value="Tando Adam"><option value="Attock">
        </datalist>

        {{-- Cart Items --}}
        <div class="pos-cart-items" id="pos-cart-items">
            {!! $initialCart !!}
        </div>

        {{-- Cart Summary --}}
        <div class="pos-cart-summary">
            <div class="coupon-row">
                <select id="pos-coupon" onchange="applyCoupon()">
                    <option value="">🏷 No Coupon</option>
                    @foreach($coupons as $c)
                    <option value="{{ $c->code }}"
                            data-type="{{ $c->type }}"
                            data-value="{{ $c->value }}"
                            data-min="{{ $c->cart_value }}">
                        {{ $c->code }} —
                        {{ $c->type === 'percent' ? $c->value.'% off' : 'Rs '.number_format($c->value, 0).' off' }}
                        @if($c->cart_value > 0)
                        (min Rs {{ number_format($c->cart_value, 0) }})
                        @endif
                    </option>
                    @endforeach
                </select>
                <div id="coupon-feedback"></div>
            </div>
            <div class="summary-row"><span>Subtotal</span><span id="sum-subtotal">Rs 0.00</span></div>
            <div class="summary-row"><span>Tax</span><span id="sum-tax">Rs 0.00</span></div>
            <div class="summary-row" id="sum-discount-row" style="display:none;"><span>Discount</span><span id="sum-discount" style="color:#e74c3c;">-Rs 0.00</span></div>
            <div class="summary-row total"><span>TOTAL</span><span id="sum-total">Rs 0.00</span></div>
        </div>

        {{-- Action Buttons --}}
        <div class="pos-actions">
            <button class="btn-hold" onclick="holdOrder()" title="Park this order and start a new one">
                HOLD
            </button>
            <input type="text" id="pos-order-note" placeholder="Order note…"
                style="flex:0 0 120px;border:1px solid #ddd;border-radius:8px;padding:8px;font-size:12px;">
            <button class="btn-checkout" id="checkout-btn" onclick="goToCheckout()" disabled>
                CHECKOUT →
            </button>
        </div>
    </div>

</div>

{{-- Session warning --}}
@if(!$session)
<div style="position:fixed;bottom:0;left:0;right:0;background:#e74c3c;color:#fff;text-align:center;padding:8px;font-size:13px;z-index:9999;">
    ⚠ No active session. <a href="{{ route('pos.sessions') }}" style="color:#fff;font-weight:700;">Open a session</a> to start processing orders.
</div>
@endif

{{-- Variant Picker Modal --}}
<div class="modal fade" id="variantModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:480px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <div>
                    <h6 class="modal-title mb-0 fw-bold" id="variantModalTitle">Select Variant</h6>
                    <small class="text-muted" id="variantModalSub">Choose a size or pack below</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- Step 1: Variant grid --}}
            <div class="modal-body pb-2" id="variantModalBody">
                <div class="text-center py-4 text-muted">Loading variants…</div>
            </div>

            {{-- Step 2: Quantity + Add to Cart — hidden until a variant is selected --}}
            <div id="variantQtyPanel" style="display:none; border-top:2px solid #f0f0f0; padding:14px 16px 16px;">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <div class="fw-bold" id="vqp-label" style="font-size:14px;"></div>
                        <div class="small" id="vqp-stock"></div>
                    </div>
                    <div class="fw-bold text-success" id="vqp-price" style="font-size:18px;"></div>
                </div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                            style="width:36px;height:36px;padding:0;font-size:18px;line-height:1;"
                            onclick="posQtyChange(-1)">−</button>
                    <input type="number" id="vqp-qty" value="1" min="1"
                           class="form-control text-center fw-bold"
                           style="width:64px;height:36px;font-size:16px;"
                           oninput="posQtyInput()">
                    <button type="button" class="btn btn-outline-secondary fw-bold"
                            style="width:36px;height:36px;padding:0;font-size:18px;line-height:1;"
                            onclick="posQtyChange(1)">+</button>
                    <div class="ms-auto text-end">
                        <div class="small text-muted">Subtotal</div>
                        <div class="fw-bold text-success" id="vqp-subtotal" style="font-size:16px;"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-success w-100 fw-bold py-2" id="vqp-add-btn"
                        onclick="posConfirmAddToCart()" style="font-size:15px;">
                    ✓ &nbsp;Add to Cart
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ─── State ───────────────────────────────────────────────────────────────────
var posState = {
    customer:          null,
    customerAddresses: [],
    orderType:         'pickup',
    selectedAddressId: null,
    selectedAddress:   '',
    selectedCity:      '',
    cartCount:         {{ $initialCartCount }},
    subtotal:          {{ $initialSubtotal }},
    tax:               {{ $initialTax }},
    couponCode:        '',
    couponDiscount:    0,
    discount:          0,
    total:             0,
};

// ─── Checkout Gate ────────────────────────────────────────────────────────────
function canCheckout(){
    if(posState.cartCount === 0) return false;

    var phone = $('#cust-phone').val().trim();
    if(phone.length < 7) return false;

    var foundVisible = $('#cust-found-panel').is(':visible');
    var newVisible   = $('#cust-new-panel').is(':visible');
    if(!foundVisible && !newVisible) return false;

    if(newVisible){
        if(!$('#cust-new-name').val().trim()) return false;
        if(posState.orderType === 'booking'){
            if(!$('#cust-new-address').val().trim()) return false;
            if(!$('#cust-new-city').val().trim()) return false;
        }
    }

    if(foundVisible && posState.orderType === 'booking'){
        if(!posState.selectedAddressId) return false;
    }

    return true;
}

function updateCheckoutBtn(){
    $('#checkout-btn').prop('disabled', !canCheckout());
}

// ─── Page-load init ───────────────────────────────────────────────────────────
$(function(){
    updateCheckoutBtn();
    renderSummary();
    restoreFromSession();
});

// ─── Search Products ──────────────────────────────────────────────────────────
var searchTimer;
$('#pos-search').on('input', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(function(){
        loadProducts($('#pos-search').val(), activeCat);
    }, 280);
});

var activeCat = '';
$('.cat-btn').on('click', function(){
    $('.cat-btn').removeClass('active');
    $(this).addClass('active');
    activeCat = $(this).data('cat');
    loadProducts($('#pos-search').val(), activeCat);
});

function loadProducts(q, cat){
    $('#pos-products-grid').html('<div style="grid-column:1/-1;text-align:center;padding:40px;color:#aaa;">Loading…</div>');
    $.get('{{ route('pos.products.search') }}', {q: q, category: cat}, function(html){
        $('#pos-products-grid').html(html);
    });
}

// Load all products on page ready
$(function(){ loadProducts('', ''); });

// ─── Variant Picker Modal ─────────────────────────────────────────────────────
var selectedVariant = null; // {productId, productName, id, label, price, stockQty}

// Reset when modal is closed
$('#variantModal').on('hidden.bs.modal', function(){
    selectedVariant = null;
    $('#variantQtyPanel').hide();
});

// Enter on qty input = add to cart
$(document).on('keydown', '#vqp-qty', function(e){
    if(e.key === 'Enter'){ e.preventDefault(); posConfirmAddToCart(); }
});

function posOpenVariantModal(productId, productName){
    selectedVariant = null;
    $('#variantModalTitle').text(productName);
    $('#variantModalSub').text('Choose a size or pack below');
    $('#variantModalBody').html('<div class="text-center py-4 text-muted">Loading variants…</div>');
    $('#variantQtyPanel').hide();
    $('#variantModal').modal('show');

    $.get('{{ url('/api/product') }}/' + productId, function(data){
        if(!data.variants || !data.variants.length){
            $('#variantModalBody').html('<p class="text-danger text-center py-3">No active variants available.</p>');
            return;
        }
        var html = '<div class="row g-2">';
        data.variants.forEach(function(v){
            var inStock = v.is_in_stock;
            html += '<div class="col-6">'
                  + '<button type="button"'
                  + ' class="btn w-100 variant-pick-btn ' + (inStock ? 'btn-outline-secondary' : 'btn-outline-light text-muted') + '"'
                  + ' id="vbtn-' + v.id + '"'
                  + ' style="text-align:left;padding:10px 12px;"'
                  + ' data-product-id="' + productId + '"'
                  + ' data-product-name="' + $('<div>').text(productName).html() + '"'
                  + ' data-variant-id="' + v.id + '"'
                  + ' data-label="' + $('<div>').text(v.display_label).html() + '"'
                  + ' data-price="' + v.price + '"'
                  + ' data-stock="' + v.stock_qty + '"'
                  + (inStock ? '' : ' disabled')
                  + '>'
                  + '<div class="fw-bold mb-1" style="font-size:13px;">' + $('<div>').text(v.variant_name).html() + '</div>'
                  + (v.weight ? '<div class="small mb-1">' + v.weight + ' ' + v.unit + '</div>' : '')
                  + '<div class="fw-bold mb-1">Rs ' + parseInt(v.price).toLocaleString() + '</div>'
                  + '<div class="small ' + (inStock ? '' : 'text-danger') + '">'
                  +   (inStock ? '✓ ' + v.stock_qty + ' in stock' : '✗ Out of stock')
                  + '</div>'
                  + '</button>'
                  + '</div>';
        });
        html += '</div>';
        $('#variantModalBody').html(html);
    }).fail(function(){
        $('#variantModalBody').html('<p class="text-danger text-center py-3">Failed to load variants.</p>');
    });
}

// Delegate clicks on variant buttons — avoids inline onclick escaping issues
$(document).on('click', '.variant-pick-btn:not([disabled])', function(){
    var btn = $(this);
    posSelectVariant(
        btn.data('product-id'),
        btn.data('product-name'),
        btn.data('variant-id'),
        btn.data('label'),
        parseFloat(btn.data('price')),
        parseInt(btn.data('stock'))
    );
});

function posSelectVariant(productId, productName, variantId, variantLabel, price, stockQty){
    selectedVariant = { productId: productId, productName: productName, id: variantId, label: variantLabel, price: price, stockQty: stockQty };

    // Highlight selected, clear others
    $('.variant-pick-btn').removeClass('btn-success text-white border-success').addClass('btn-outline-secondary');
    $('#vbtn-' + variantId).removeClass('btn-outline-secondary').addClass('btn-success text-white border-success');

    // Update qty panel
    $('#vqp-label').text(variantLabel);
    $('#vqp-stock').html(stockQty > 0
        ? '<span class="text-success">✓ ' + stockQty + ' in stock</span>'
        : '<span class="text-danger">Out of stock</span>');
    $('#vqp-price').text('Rs ' + parseInt(price).toLocaleString());
    $('#vqp-qty').val(1).attr('max', stockQty);
    $('#vqp-add-btn').prop('disabled', false).html('✓ &nbsp;Add to Cart');
    posUpdateSubtotal();

    // Slide panel into view
    $('#variantQtyPanel').slideDown(150);
    setTimeout(function(){ $('#vqp-qty').focus().select(); }, 200);
}

function posQtyChange(delta){
    var max = parseInt($('#vqp-qty').attr('max') || 9999);
    var qty = Math.max(1, Math.min(max, (parseInt($('#vqp-qty').val()) || 1) + delta));
    $('#vqp-qty').val(qty);
    posUpdateSubtotal();
}

function posQtyInput(){
    var max  = parseInt($('#vqp-qty').attr('max') || 9999);
    var qty  = Math.max(1, Math.min(max, parseInt($('#vqp-qty').val()) || 1));
    $('#vqp-qty').val(qty);
    posUpdateSubtotal();
}

function posUpdateSubtotal(){
    if(!selectedVariant) return;
    var qty = parseInt($('#vqp-qty').val() || 1);
    var sub = qty * parseFloat(selectedVariant.price);
    $('#vqp-subtotal').text('Rs ' + sub.toLocaleString());
}

function posConfirmAddToCart(){
    if(!selectedVariant){ return; }
    var qty = parseInt($('#vqp-qty').val() || 1);
    if(qty < 1){ alert('Quantity must be at least 1.'); return; }

    $('#vqp-add-btn').prop('disabled', true).text('Adding…');

    $.post('{{ route('pos.cart.add') }}', {
        product_id: selectedVariant.productId,
        variant_id: selectedVariant.id,
        quantity:   qty,
    }, function(res){
        if(res.success){
            $('#variantModal').modal('hide');
            $('#pos-cart-items').html(res.cart);
            posState.cartCount = res.count;
            updateCheckoutBtn();
            updateSummaryFromServer();
        } else {
            alert(res.message || 'Failed to add to cart.');
            $('#vqp-add-btn').prop('disabled', false).html('✓ &nbsp;Add to Cart');
        }
    }).fail(function(){
        alert('Server error. Please try again.');
        $('#vqp-add-btn').prop('disabled', false).html('✓ &nbsp;Add to Cart');
    });
}

// ─── Update Cart Qty ──────────────────────────────────────────────────────────
function posUpdateCart(rowId, qty){
    $.ajax({
        type: 'PUT',
        url: '{{ url('pos/cart/update') }}/' + rowId,
        data: {quantity: qty},
        success: function(res){
            if(res.success){
                $('#pos-cart-items').html(res.cart);
                posState.cartCount = res.count;
                updateCheckoutBtn();
                updateSummaryFromServer();
            }
        }
    });
}

// ─── Remove from Cart ────────────────────────────────────────────────────────
function posRemoveFromCart(rowId){
    $.ajax({
        type: 'DELETE',
        url: '{{ url('pos/cart/remove') }}/' + rowId,
        success: function(res){
            if(res.success){
                $('#pos-cart-items').html(res.cart);
                posState.cartCount = res.count;
                updateCheckoutBtn();
                updateSummaryFromServer();
            }
        }
    });
}

// ─── Cart Summary ────────────────────────────────────────────────────────────

// Called after AJAX cart ops: syncs subtotal/tax from cart partial, then re-evaluates coupon.
function updateSummaryFromServer(){
    posState.subtotal = parseFloat($('#cart-subtotal').val()) || 0;
    posState.tax      = parseFloat($('#cart-tax').val()) || 0;
    applyCoupon();
}

// Reads the selected coupon, validates eligibility against current subtotal, calculates discount.
function applyCoupon(){
    var code = $('#pos-coupon').val();

    if(!code){
        posState.couponCode     = '';
        posState.couponDiscount = 0;
        $('#coupon-feedback').text('');
        renderSummary();
        return;
    }

    var opt     = $('#pos-coupon option:selected');
    var type    = opt.data('type');
    var value   = parseFloat(opt.data('value')) || 0;
    var minCart = parseFloat(opt.data('min'))   || 0;

    if(posState.subtotal < minCart){
        posState.couponCode     = '';
        posState.couponDiscount = 0;
        $('#coupon-feedback').html(
            '<span style="color:#e74c3c;">⚠ Min cart Rs ' +
            minCart.toLocaleString('en-PK', {maximumFractionDigits:0}) +
            ' required</span>'
        );
        renderSummary();
        return;
    }

    var discount = (type === 'fixed') ? value : (posState.subtotal * value / 100);
    posState.couponCode     = code;
    posState.couponDiscount = discount;

    var saving = (type === 'percent')
        ? value + '% off = -Rs ' + Math.round(discount).toLocaleString('en-PK')
        : '-Rs ' + Math.round(discount).toLocaleString('en-PK');
    $('#coupon-feedback').html('<span style="color:#2ecc71;">✓ ' + saving + '</span>');

    renderSummary();
}

// Renders the totals using cached posState values — never reads DOM inputs directly.
function renderSummary(){
    var disc  = posState.couponDiscount || 0;
    var total = Math.max(0, posState.subtotal + posState.tax - disc);
    posState.discount = disc;
    posState.total    = total;

    $('#sum-subtotal').text('Rs ' + posState.subtotal.toFixed(2));
    $('#sum-tax').text('Rs ' + posState.tax.toFixed(2));
    if(disc > 0){
        $('#sum-discount').text('-Rs ' + disc.toFixed(2));
        $('#sum-discount-row').show();
    } else {
        $('#sum-discount-row').hide();
    }
    $('#sum-total').text('Rs ' + total.toFixed(2));
}

// ─── Customer Lookup ─────────────────────────────────────────────────────────
var custLookupTimer;
$('#cust-phone').on('input', function(){
    var phone = $(this).val().trim();
    $('#cust-clear-btn').toggle(phone.length > 0);
    clearTimeout(custLookupTimer);
    if(phone.length < 7){ resetCustomerState(); return; }
    $('#cust-status').text('Searching…').css('color','#aaa');
    custLookupTimer = setTimeout(function(){ lookupPhone(phone); }, 500);
    updateCheckoutBtn();
});

$('#cust-new-name, #cust-new-address, #cust-new-city').on('input', function(){
    updateCheckoutBtn();
});

function lookupPhone(phone){
    $.get('{{ route('pos.customer.lookup') }}', {phone: phone}, function(res){
        if(res.found){
            handleFoundCustomer(res.customer, res.addresses);
        } else {
            handleNewCustomer();
        }
    }).fail(function(){ $('#cust-status').text('Lookup failed.').css('color','#e74c3c'); });
}

function handleFoundCustomer(customer, addresses){
    posState.customer          = customer;
    posState.customerAddresses = addresses;
    $('#cust-found-name').text(customer.name);
    $('#cust-status').text('').css('color','#aaa');
    $('#cust-found-panel').show();
    $('#cust-new-panel').hide();
    renderAddresses(addresses);
    setOrderType(posState.orderType);
    updateCheckoutBtn();
}

function handleNewCustomer(){
    posState.customer          = null;
    posState.customerAddresses = [];
    posState.selectedAddressId = null;
    posState.selectedAddress   = '';
    posState.selectedCity      = '';
    $('#cust-status').text('').css('color','#aaa');
    $('#cust-new-panel').show();
    $('#cust-found-panel').hide();
    $('#cust-new-name, #cust-new-address, #cust-new-city').val('');
    setOrderType(posState.orderType);
    updateCheckoutBtn();
}

function resetCustomerState(){
    posState.customer          = null;
    posState.customerAddresses = [];
    posState.selectedAddressId = null;
    posState.selectedAddress   = '';
    posState.selectedCity      = '';
    $('#cust-found-panel, #cust-new-panel').hide();
    $('#cust-status').text('').css('color','#aaa');
    updateCheckoutBtn();
}

function clearCustomer(){
    $('#cust-phone').val('');
    $('#cust-clear-btn').hide();
    resetCustomerState();
    setOrderType('pickup');
}

// ─── Order Type Toggle ────────────────────────────────────────────────────────
function setOrderType(type){
    posState.orderType         = type;
    posState.selectedAddressId = null;
    posState.selectedAddress   = '';
    posState.selectedCity      = '';

    // Found panel buttons
    $('#ot-found-pickup').toggleClass('active', type === 'pickup');
    $('#ot-found-delivery').toggleClass('active', type === 'booking');
    $('#found-delivery-wrap').toggle(type === 'booking');

    // New panel buttons
    $('#ot-new-pickup').toggleClass('active', type === 'pickup');
    $('#ot-new-booking').toggleClass('active', type === 'booking');
    $('#new-booking-wrap').toggle(type === 'booking');

    updateCheckoutBtn();
}

// ─── Address Rendering ────────────────────────────────────────────────────────
var addrIcons = {Home:'🏠', Office:'💼', Other:'📦'};

function escHtml(s){ return $('<div>').text(s).html(); }
function escStr(s){ return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'"); }

function renderAddresses(addresses){
    var html = '';
    if(!addresses || !addresses.length){
        html = '<div style="font-size:11px;color:#aaa;padding:3px 0;">No saved addresses.</div>';
    } else {
        addresses.forEach(function(a){
            var icon = addrIcons[a.title] || '📍';
            html += '<div class="addr-item" id="addr-item-' + a.id + '"'
                  + ' onclick="selectAddress(' + a.id + ',\'' + escStr(a.address) + '\',\'' + escStr(a.city) + '\')">'
                  + '<span class="addr-item-title">' + icon + ' ' + escHtml(a.title) + '</span>'
                  + '<span class="addr-item-text">' + escHtml(a.address) + ', ' + escHtml(a.city) + '</span>'
                  + '<button class="addr-item-edit" onclick="event.stopPropagation();editAddress(' + a.id + ')" title="Edit">✏</button>'
                  + '</div>';
        });
    }
    $('#cust-addr-list').html(html);

    // Auto-select default if in booking mode
    if(posState.orderType === 'booking'){
        var def = addresses.find(function(a){ return a.is_default; });
        if(def) selectAddress(def.id, def.address, def.city);
    }
}

function selectAddress(id, address, city){
    posState.selectedAddressId = id;
    posState.selectedAddress   = address;
    posState.selectedCity      = city;
    $('.addr-item').removeClass('selected');
    $('#addr-item-' + id).addClass('selected');
    cancelAddressForm();
    updateCheckoutBtn();
}

function showAddressForm(){
    $('#edit-addr-id').val('');
    $('#addr-form-title').val('Home');
    $('#addr-form-address, #addr-form-city').val('');
    $('#addr-form-default').prop('checked', false);
    $('#cust-addr-form').slideDown(150);
    $('#cust-add-addr-btn').hide();
}

function editAddress(id){
    var addr = posState.customerAddresses.find(function(a){ return a.id === id; });
    if(!addr) return;
    $('#edit-addr-id').val(addr.id);
    $('#addr-form-title').val(addr.title);
    $('#addr-form-address').val(addr.address);
    $('#addr-form-city').val(addr.city);
    $('#addr-form-default').prop('checked', addr.is_default);
    $('#cust-addr-form').slideDown(150);
    $('#cust-add-addr-btn').hide();
}

function cancelAddressForm(){
    $('#cust-addr-form').slideUp(100);
    $('#cust-add-addr-btn').show();
}

function saveAddress(){
    var addrId  = $('#edit-addr-id').val();
    var title   = $('#addr-form-title').val();
    var address = $('#addr-form-address').val().trim();
    var city    = $('#addr-form-city').val().trim();
    var isDef   = $('#addr-form-default').is(':checked') ? 1 : 0;

    if(!address || !city){ alert('Address and city are required.'); return; }
    if(!posState.customer){ return; }

    $.post('{{ route('pos.customer.address.save') }}', {
        customer_id: posState.customer.id,
        address_id:  addrId || '',
        title:       title,
        address:     address,
        city:        city,
        is_default:  isDef,
    }, function(res){
        if(!res.success){ alert('Failed to save address.'); return; }
        var a = res.address;
        if(isDef) posState.customerAddresses.forEach(function(x){ x.is_default = false; });
        if(addrId){
            var idx = posState.customerAddresses.findIndex(function(x){ return x.id == addrId; });
            if(idx !== -1) posState.customerAddresses[idx] = a;
        } else {
            posState.customerAddresses.push(a);
        }
        renderAddresses(posState.customerAddresses);
        cancelAddressForm();
        if(posState.orderType === 'booking') selectAddress(a.id, a.address, a.city);
    }).fail(function(){ alert('Failed to save address. Please try again.'); });
}

// ─── Gift Toggle ─────────────────────────────────────────────────────────────
function toggleGift(on){
    if(on){
        $('#gift-panel').slideDown(200);
        prefillSenderFromCustomer();
    } else {
        $('#gift-panel').slideUp(200);
    }
}

function prefillSenderFromCustomer(){
    var name = '', phone = '', address = '', city = '';

    if(posState.customer){
        name  = posState.customer.name  || '';
        phone = posState.customer.mobile || '';
        if(posState.selectedAddress){
            address = posState.selectedAddress;
            city    = posState.selectedCity;
        } else {
            var def = posState.customerAddresses.find(function(a){ return a.is_default; });
            if(!def && posState.customerAddresses.length) def = posState.customerAddresses[0];
            if(def){ address = def.address; city = def.city; }
        }
    } else if($('#cust-new-panel').is(':visible')){
        name  = $('#cust-new-name').val().trim();
        phone = $('#cust-phone').val().trim();
        if(posState.orderType === 'booking'){
            address = $('#cust-new-address').val().trim();
            city    = $('#cust-new-city').val().trim();
        }
    }

    if(name    && !$('#gift-sender-name').val())    $('#gift-sender-name').val(name);
    if(phone   && !$('#gift-sender-phone').val())   $('#gift-sender-phone').val(phone);
    if(address && !$('#gift-sender-address').val()) $('#gift-sender-address').val(address);
    if(city    && !$('#gift-sender-city').val())    $('#gift-sender-city').val(city);
}

// ─── Receiver Phone Lookup ────────────────────────────────────────────────────
var receiverLookupTimer;
$('#gift-receiver-phone').on('input', function(){
    var phone = $(this).val().trim();
    clearTimeout(receiverLookupTimer);
    $('#gift-receiver-status').hide().text('');
    if(phone.length < 7) return;
    receiverLookupTimer = setTimeout(function(){ lookupReceiverPhone(phone); }, 500);
});

function lookupReceiverPhone(phone){
    $.get('{{ route('pos.customer.lookup') }}', {phone: phone}, function(res){
        if(res.found){
            var c = res.customer;
            if(!$('#gift-receiver-name').val().trim()) $('#gift-receiver-name').val(c.name);
            var def = res.addresses && (res.addresses.find(function(a){ return a.is_default; }) || res.addresses[0]);
            if(def){
                if(!$('#gift-address').val().trim())       $('#gift-address').val(def.address);
                if(!$('#gift-receiver-city').val().trim()) $('#gift-receiver-city').val(def.city);
            }
            $('#gift-receiver-status').text('✓ ' + c.name + ' found').css('color','#2ecc71').show();
        } else {
            $('#gift-receiver-status').text('New receiver').css('color','#aaa').show();
        }
    });
}

// ─── Hold Order ───────────────────────────────────────────────────────────────
function holdOrder(){
    if(posState.cartCount === 0){ alert('Cart is empty.'); return; }
    var customer = getCustomerData();
    var gift     = getGiftData();
    var note     = $('#pos-order-note').val();
    $.post('{{ route('pos.hold') }}', {customer: customer, gift: gift, note: note}, function(res){
        if(res.success){
            alert('Order parked. You can resume it from the Held Orders page.');
            $('#pos-cart-items').html(res.cart);
            $('#held-badge').text(res.heldCount);
            posState.cartCount = 0;
            updateCheckoutBtn();
            clearPosSession();
            resetGiftForm();
            $('#pos-order-note').val('');
            clearCustomer();
            updateSummaryFromServer();
        } else {
            alert(res.message);
        }
    });
}

// ─── Checkout ─────────────────────────────────────────────────────────────────
function goToCheckout(){
    if(posState.cartCount === 0){ alert('Cart is empty.'); return; }

    var cust = getCustomerData();
    sessionStorage.setItem('pos_customer_id',       cust.id || '');
    sessionStorage.setItem('pos_customer_phone',    cust.phone || '');
    sessionStorage.setItem('pos_customer_name',     cust.name || '');
    sessionStorage.setItem('pos_order_type',        cust.order_type || 'pickup');
    sessionStorage.setItem('pos_address_id',        cust.address_id || '');
    sessionStorage.setItem('pos_delivery_address',  cust.delivery_address || '');
    sessionStorage.setItem('pos_delivery_city',     cust.delivery_city || '');
    sessionStorage.setItem('pos_save_customer',     String(cust.save_customer || 0));
    sessionStorage.setItem('pos_coupon_code',       posState.couponCode || '');
    sessionStorage.setItem('pos_discount',          String(posState.couponDiscount || 0));
    sessionStorage.setItem('pos_order_note',        $('#pos-order-note').val());

    var gift = getGiftData();
    if(gift){
        sessionStorage.setItem('pos_is_gift',                '1');
        sessionStorage.setItem('pos_gift_sender_name',       gift.sender_name || '');
        sessionStorage.setItem('pos_gift_sender_phone',      gift.sender_phone || '');
        sessionStorage.setItem('pos_gift_sender_address',    gift.sender_address || '');
        sessionStorage.setItem('pos_gift_sender_city',       gift.sender_city || '');
        sessionStorage.setItem('pos_gift_receiver_name',     gift.receiver_name || '');
        sessionStorage.setItem('pos_gift_receiver_phone',    gift.receiver_phone || '');
        sessionStorage.setItem('pos_gift_receiver_address',  gift.receiver_address || '');
        sessionStorage.setItem('pos_gift_receiver_city',     gift.receiver_city || '');
        sessionStorage.setItem('pos_gift_message',           gift.gift_message || '');
        sessionStorage.setItem('pos_gift_wrapping',          gift.gift_wrapping || '0');
    } else {
        sessionStorage.setItem('pos_is_gift', '0');
    }

    window.location = '{{ route('pos.checkout') }}';
}

// ─── Helpers ─────────────────────────────────────────────────────────────────
function getCustomerData(){
    var phone = $('#cust-phone').val().trim();
    if(posState.customer){
        return {
            type:             'found',
            id:               posState.customer.id,
            name:             posState.customer.name,
            phone:            posState.customer.mobile,
            order_type:       posState.orderType,
            address_id:       posState.selectedAddressId || '',
            delivery_address: posState.selectedAddress || '',
            delivery_city:    posState.selectedCity || '',
            save_customer:    0,
        };
    }
    var isBooking = posState.orderType === 'booking';
    return {
        type:             'new',
        id:               '',
        name:             $('#cust-new-name').val().trim(),
        phone:            phone,
        order_type:       posState.orderType,
        address_id:       '',
        delivery_address: isBooking ? $('#cust-new-address').val().trim() : '',
        delivery_city:    isBooking ? $('#cust-new-city').val().trim() : '',
        save_customer:    1,
    };
}

function getGiftData(){
    if(!$('#gift-toggle').is(':checked')) return null;
    return {
        is_gift:          1,
        sender_name:      $('#gift-sender-name').val(),
        sender_phone:     $('#gift-sender-phone').val(),
        sender_address:   $('#gift-sender-address').val(),
        sender_city:      $('#gift-sender-city').val(),
        receiver_name:    $('#gift-receiver-name').val(),
        receiver_phone:   $('#gift-receiver-phone').val(),
        receiver_address: $('#gift-address').val(),
        receiver_city:    $('#gift-receiver-city').val(),
        gift_message:     $('#gift-message').val(),
        gift_wrapping:    $('#gift-wrapping').is(':checked') ? 1 : 0,
    };
}

function resetGiftForm(){
    $('#gift-toggle').prop('checked', false);
    $('#gift-panel').hide();
    $('#gift-sender-name, #gift-sender-phone, #gift-sender-address, #gift-sender-city').val('');
    $('#gift-receiver-name, #gift-receiver-phone, #gift-address, #gift-receiver-city, #gift-message').val('');
    $('#gift-receiver-status').hide().text('');
    $('#gift-wrapping').prop('checked', false);
}

// ─── Session Persistence ─────────────────────────────────────────────────────
function clearPosSession(){
    ['pos_customer_id','pos_customer_phone','pos_customer_name',
     'pos_order_type','pos_address_id','pos_delivery_address','pos_delivery_city',
     'pos_save_customer','pos_coupon_code','pos_discount','pos_order_note',
     'pos_is_gift','pos_gift_sender_name','pos_gift_sender_phone',
     'pos_gift_sender_address','pos_gift_sender_city','pos_gift_receiver_name',
     'pos_gift_receiver_phone','pos_gift_receiver_address','pos_gift_receiver_city',
     'pos_gift_message','pos_delivery_date','pos_gift_wrapping'
    ].forEach(function(k){ sessionStorage.removeItem(k); });
}

function restoreFromSession(){
    // Don't restore if cart is empty — order has been completed or nothing to restore
    if(posState.cartCount === 0) return;

    var phone = sessionStorage.getItem('pos_customer_phone');
    if(!phone) return;

    var customerId   = sessionStorage.getItem('pos_customer_id') || '';
    var customerName = sessionStorage.getItem('pos_customer_name') || '';
    var orderType    = sessionStorage.getItem('pos_order_type') || 'pickup';
    var addressId    = sessionStorage.getItem('pos_address_id') || '';
    var deliveryAddr = sessionStorage.getItem('pos_delivery_address') || '';
    var deliveryCity = sessionStorage.getItem('pos_delivery_city') || '';
    var couponCode   = sessionStorage.getItem('pos_coupon_code') || '';
    var orderNote    = sessionStorage.getItem('pos_order_note') || '';
    var isGift       = sessionStorage.getItem('pos_is_gift') === '1';

    // Restore phone field and show clear button
    $('#cust-phone').val(phone);
    $('#cust-clear-btn').show();

    if(customerId){
        // Existing customer: re-lookup to get fresh address list
        $('#cust-status').text('Restoring…').css('color','#aaa');
        $.get('{{ route('pos.customer.lookup') }}', {phone: phone}, function(res){
            if(res.found){
                handleFoundCustomer(res.customer, res.addresses);
                setOrderType(orderType);
                if(orderType === 'booking' && addressId){
                    // Give DOM a tick to show the delivery panel before selecting
                    setTimeout(function(){
                        selectAddress(parseInt(addressId), deliveryAddr, deliveryCity);
                    }, 50);
                }
            } else {
                handleNewCustomer();
            }
        }).fail(function(){
            $('#cust-status').text('').css('color','#aaa');
        });
    } else {
        // New customer: restore panel directly without a network call
        handleNewCustomer();
        $('#cust-new-name').val(customerName);
        setOrderType(orderType);
        if(orderType === 'booking'){
            $('#cust-new-address').val(deliveryAddr);
            $('#cust-new-city').val(deliveryCity);
        }
        updateCheckoutBtn();
    }

    if(couponCode){
        $('#pos-coupon').val(couponCode);
        applyCoupon();
    }

    if(orderNote) $('#pos-order-note').val(orderNote);

    if(isGift){
        $('#gift-toggle').prop('checked', true);
        $('#gift-panel').show();
        $('#gift-sender-name').val(sessionStorage.getItem('pos_gift_sender_name') || '');
        $('#gift-sender-phone').val(sessionStorage.getItem('pos_gift_sender_phone') || '');
        $('#gift-sender-address').val(sessionStorage.getItem('pos_gift_sender_address') || '');
        $('#gift-sender-city').val(sessionStorage.getItem('pos_gift_sender_city') || '');
        $('#gift-receiver-name').val(sessionStorage.getItem('pos_gift_receiver_name') || '');
        $('#gift-receiver-phone').val(sessionStorage.getItem('pos_gift_receiver_phone') || '');
        $('#gift-address').val(sessionStorage.getItem('pos_gift_receiver_address') || '');
        $('#gift-receiver-city').val(sessionStorage.getItem('pos_gift_receiver_city') || '');
        $('#gift-message').val(sessionStorage.getItem('pos_gift_message') || '');
        $('#gift-wrapping').prop('checked', sessionStorage.getItem('pos_gift_wrapping') === '1');
    }
}

// ─── Keyboard shortcuts ───────────────────────────────────────────────────────
$(document).on('keydown', function(e){
    if(e.key === 'F1'){ e.preventDefault(); $('#pos-search').focus(); }
    if(e.key === 'F2'){ e.preventDefault(); holdOrder(); }
    if(e.key === 'F3' && !$('#checkout-btn').prop('disabled')){ e.preventDefault(); goToCheckout(); }
});
</script>
@endpush
