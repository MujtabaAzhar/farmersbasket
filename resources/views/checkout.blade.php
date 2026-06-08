@extends('layouts.app')
@section('content')
<style>
    .form-check .form-check-input {
    float: left;
    margin-left: 0 !important;
}
</style>

@php
    $savedName    = old('name',    $address?->name    ?? auth()->user()?->name    ?? '');
    $savedEmail   = old('email',   auth()->user()?->email ?? '');
    $savedPhone   = old('phone',   $address?->phone   ?? auth()->user()?->mobile  ?? '');
    $savedCity    = old('city',    $address?->city    ?? '');
    $savedAddress = old('address', $address?->address ?? '');
@endphp

<!-- Hero section -->
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url(assets/img/hero/breadcrumb-banner.jpg);">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">Checkout</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="index.html">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li><a href="{{ route('cart.index') }}">Cart</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>Checkout</li>
            </ul>
        </div>
    </div>
    <img src="assets/img/home-1/home-shape-start.png" alt="img" class="bread-shape-start position-absolute">
    <img src="assets/img/home-1/home-shape-end.png" alt="img" class="bread-shape-end position-absolute d-sm-block d-none">
</section>

<section class="shop-section position-relative z-1 fix section-padding">
    <div class="container">
        <form action="{{ route('cart.place.an.order') }}" method="POST" class="billing-form" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">

                {{-- ── LEFT: Billing / Gift Details ──────────────────────────────── --}}
                <div class="col-lg-8">
                    <div class="checkout-billing-details h-100">

                        @if($errors->any())
                        <div class="alert alert-danger mb-4">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                        @endif

                        <h4 class="text-black mb-lg-4 mb-3">Billing Details</h4>

                        {{-- Gift toggle --}}
                        <input type="hidden" name="gift" id="gift-value" value="{{ old('gift', 'No') }}">
                        <div class="form-check mb-4 p-3 border rounded" style="background:#fff8e1;">
                            <input class="form-check-input" type="checkbox" id="gift-checkbox"
                                   style="width:18px;height:18px;cursor:pointer;"
                                   {{ old('gift') === 'Yes' ? 'checked' : '' }}>
                            <label class="form-check-label ms-2 fw-600 fs-16" for="gift-checkbox" style="cursor:pointer;">
                                🎁 Send this order as a gift
                            </label>
                        </div>

                        {{-- ── REGULAR ORDER ──────────────────────────────────── --}}
                        <div id="section-regular" class="{{ old('gift') === 'Yes' ? 'd-none' : '' }}">
                            <h5 class="mb-3 text-muted fw-500">Your Delivery Details</h5>

                            {{-- Hidden required fields --}}
                            <input type="hidden" name="locality" value="{{ old('locality', 'N/A') }}">
                            <input type="hidden" name="landmark" value="{{ old('landmark', 'N/A') }}">
                            <input type="hidden" name="zip"      value="{{ old('zip', '00000') }}">
                            <input type="hidden" name="state"    value="{{ old('state', 'Punjab') }}">
                            <input type="hidden" name="country"  value="{{ old('country', 'Pakistan') }}">

                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <input type="text" name="name" class="form-control" placeholder="Full Name *"
                                           value="{{ $savedName }}" required>
                                    @error('name')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="email" name="email" class="form-control" placeholder="Email Address *"
                                           value="{{ $savedEmail }}" required>
                                    @error('email')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="tel" name="phone" class="form-control" placeholder="Phone (03xxxxxxxxx) *"
                                           pattern="03[0-9]{9}" maxlength="11"
                                           title="Enter a valid Pakistani number starting with 03"
                                           value="{{ $savedPhone }}" required>
                                    @error('phone')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="city" list="pak-cities"
                                           class="form-control" placeholder="City — type to search *"
                                           autocomplete="off" value="{{ $savedCity }}" required>
                                    @error('city')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                                <div class="col-12">
                                    <input type="text" name="address" class="form-control" placeholder="Delivery Address *"
                                           value="{{ $savedAddress }}" required>
                                    @error('address')<span class="text-danger small">{{ $message }}</span>@enderror
                                </div>
                            </div>
                        </div>

                        {{-- ── GIFT ORDER ─────────────────────────────────────── --}}
                        <div id="section-gift" class="{{ old('gift') !== 'Yes' ? 'd-none' : '' }}">
                            <div class="row g-4">

                                {{-- FROM: Sender --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded" style="background:#f0fdf4;border:1px solid #c8e6c9;">
                                        <h5 class="mb-3" style="color:#2e7d32;">📤 FROM: Sender Details</h5>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="gift_sender_name"
                                                   placeholder="Your full name *"
                                                   value="{{ old('gift_sender_name', auth()->user()?->name ?? '') }}">
                                            @error('gift_sender_name')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <input type="tel" class="form-control" name="gift_sender_phone"
                                                   placeholder="Your phone (03xxxxxxxxx) *"
                                                   pattern="03[0-9]{9}" maxlength="11"
                                                   value="{{ old('gift_sender_phone', auth()->user()?->mobile ?? '') }}">
                                            @error('gift_sender_phone')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        @guest
                                        <div class="mb-3">
                                            <input type="email" class="form-control" name="gift_sender_email"
                                                   placeholder="Your email address *"
                                                   value="{{ old('gift_sender_email') }}">
                                            @error('gift_sender_email')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        @endguest
                                        <div class="mb-0">
                                            <input type="text" class="form-control" name="gift_sender_address"
                                                   placeholder="Your address (optional)"
                                                   value="{{ old('gift_sender_address') }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- TO: Receiver --}}
                                <div class="col-sm-6">
                                    <div class="p-3 rounded" style="background:#fff8e1;border:1px solid #ffe082;">
                                        <h5 class="mb-3" style="color:#e65100;">📦 TO: Delivery Details</h5>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="gift_receiver_name"
                                                   placeholder="Recipient full name *"
                                                   value="{{ old('gift_receiver_name') }}">
                                            @error('gift_receiver_name')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <input type="tel" class="form-control" name="gift_receiver_phone"
                                                   placeholder="Recipient phone (03xxxxxxxxx) *"
                                                   pattern="03[0-9]{9}" maxlength="11"
                                                   value="{{ old('gift_receiver_phone') }}">
                                            @error('gift_receiver_phone')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <input type="text" class="form-control" name="gift_receiver_city"
                                                   list="pak-cities" autocomplete="off"
                                                   placeholder="Recipient city — type to search *"
                                                   value="{{ old('gift_receiver_city') }}">
                                        </div>
                                        <div class="mb-3">
                                            <textarea class="form-control" name="gift_receiver_address" rows="2"
                                                      placeholder="Full delivery address *">{{ old('gift_receiver_address') }}</textarea>
                                            @error('gift_receiver_address')<span class="text-danger small">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="mb-0">
                                            <textarea class="form-control" name="gift_message" rows="2"
                                                      placeholder="💌 Gift message (optional)">{{ old('gift_message') }}</textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- ── PAYMENT METHODS ────────────────────────────────── --}}
                        <div class="payment-methods mt-4 mb-3">
                            <h5 class="mb-3 border-bottom pb-2">Payment Method</h5>

                            {{-- Bank Transfer --}}
                            <div class="form-check border rounded p-3 mb-3 bg-light d-flex align-items-start">
                                <input class="form-check-input ms-0 me-3 mt-1" type="radio"
                                       name="mode" value="bank_transfer" id="mode_bank"
                                       {{ old('mode') === 'bank_transfer' ? 'checked' : '' }}>
                                <label class="form-check-label flex-grow-1" for="mode_bank">
                                    <span class="fs-16 fw-semibold text-dark d-block">🏦 Bank Transfer</span>
                                    <div class="payment-details mt-2" id="details-bank_transfer" style="display:none;">
                                        <div class="text-muted mb-3">
                                            <strong class="text-dark fs-15">Meezan Bank</strong><br>
                                            <span class="fs-14">Title: Mujtaba Azhar</span><br>
                                            <span class="fs-14">Account No: 123456789</span>
                                        </div>
                                        <div class="pt-2 border-top">
                                            <label for="picture" class="form-label fs-14 fw-semibold text-dark mb-1">
                                                Upload Payment Receipt <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="picture" id="picture"
                                                   class="form-control form-control-sm"
                                                   accept="image/*,.pdf">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            {{-- JazzCash --}}
                            <div class="form-check border rounded p-3 mb-3 bg-light d-flex align-items-start">
                                <input class="form-check-input ms-0 me-3 mt-1" type="radio"
                                       name="mode" value="wallet" id="mode_jazz"
                                       {{ old('mode') === 'wallet' ? 'checked' : '' }}>
                                <label class="form-check-label flex-grow-1" for="mode_jazz">
                                    <span class="fs-16 fw-semibold text-dark d-block">📱 JazzCash</span>
                                    <div class="payment-details mt-2" id="details-wallet" style="display:none;">
                                        <div class="text-muted mb-3">
                                            <strong class="text-dark fs-15">JazzCash</strong><br>
                                            <span class="fs-14">Title: Mujtaba Azhar</span><br>
                                            <span class="fs-14">Account No: 123456789</span>
                                        </div>
                                        <div class="pt-2 border-top">
                                            <label for="wallet_picture" class="form-label fs-14 fw-semibold text-dark mb-1">
                                                Upload Transfer Screenshot <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="wallet_picture" id="wallet_picture"
                                                   class="form-control form-control-sm"
                                                   accept="image/*,.pdf">
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <small class="d-block mt-2 fs-12 text-muted">
                                Transfer the exact order total and upload your receipt. Your order will be processed after payment verification.
                            </small>
                        </div>

                    </div>
                </div>

                {{-- ── RIGHT: Order Summary ───────────────────────────────────────── --}}
                <div class="col-lg-4">
                    <div class="shadow-cus d-flex flex-column justify-content-between coupon-group position-relative h-100 p-xl-4 p-3 rounded-3 bg-white">
                        <div>
                            <h5 class="border-bottom pb-2 mb-3">Order Summary</h5>
                            <div class="d-flex flex-column gap-3 align-items-center pb-4">
                                @foreach(Cart::instance('cart')->content() as $item)
                                <div class="order-summary d-flex justify-content-between w-100 gap-2 border-bottom pb-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            <h5 class="text-black fs-14 lh-1 max-w-180 fw-500">{{ $item->name }} × {{ $item->qty }}</h5>
                                            @if($item->options->get('variant_label'))
                                                <small class="fs-12 text-black fw-medium">{{ $item->options->get('variant_label') }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="fw-semibold theme-clr fs-14">Rs {{ $item->subtotal() }}</span>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="order-summary-footer">
                            @php
                                $cartQty   = (int) Cart::instance('cart')->count();
                                $baseFee   = (float) \App\Models\Setting::get('shipping_fee', config('cart.shipping', 0));
                                $shipTotal = (float) (Session::get('checkout')['shipping'] ?? Session::get('discounts')['shipping'] ?? ($baseFee * max(1, $cartQty)));
                            @endphp
                            @if(Session::has('discounts'))
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between border-top pt-2 pb-1">
                                        <span class="fs-12 text-black fw-medium">Subtotal</span>
                                        <span class="fs-12 text-black fw-medium">Rs {{ Session::get('discounts')['subtotal'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pb-2">
                                        <span class="fs-12 text-black fw-medium">{{ Session::get('coupon')['code'] }}</span>
                                        <span class="fs-12 text-black fw-medium">- Rs {{ Session::get('discounts')['discount'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pb-2">
                                        <div>
                                            <span class="fs-12 text-black fw-medium">Shipping</span>
                                            <div class="text-muted" style="font-size:11px;">{{ $cartQty }} × Rs {{ number_format($baseFee, 0) }}</div>
                                        </div>
                                        <span class="fs-12 text-black fw-medium">Rs {{ number_format($shipTotal, 0) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-2 pb-3">
                                        <span class="fs-12 text-black fw-medium"><strong>Total</strong></span>
                                        <span class="fs-12 text-black fw-medium"><strong>Rs {{ Session::get('discounts')['total'] }}</strong></span>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex flex-column">
                                    <div class="d-flex align-items-center justify-content-between border-top pt-2 pb-1">
                                        <span class="fs-12 text-black fw-medium">Subtotal</span>
                                        <span class="fs-12 text-black fw-medium">Rs {{ Session::get('checkout')['subtotal'] ?? Cart::instance('cart')->subtotal() }}</span>
                                    </div>
                                    @if((Session::get('checkout')['tax'] ?? 0) > 0)
                                    <div class="d-flex align-items-center justify-content-between pb-1">
                                        <span class="fs-12 text-black fw-medium">Tax</span>
                                        <span class="fs-12 text-black fw-medium">Rs {{ Session::get('checkout')['tax'] }}</span>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-between pb-2">
                                        <div>
                                            <span class="fs-12 text-black fw-medium">Shipping</span>
                                            <div class="text-muted" style="font-size:11px;">{{ $cartQty }} × Rs {{ number_format($baseFee, 0) }}</div>
                                        </div>
                                        <span class="fs-12 text-black fw-medium">Rs {{ number_format($shipTotal, 0) }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between border-top pt-2 pb-3">
                                        <span class="fs-12 text-black fw-medium"><strong>Total</strong></span>
                                        <span class="fs-12 text-black fw-medium"><strong>Rs {{ Session::get('checkout')['total'] ?? Cart::instance('cart')->total() }}</strong></span>
                                    </div>
                                </div>
                            @endif

                            <button type="submit" class="theme-btn text-center justify-content-center w-100">
                                Confirm Order
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </div>
    <img src="assets/img/inner-global-pasta.png" alt="img"
         class="position-absolute bottom-0 end-0 float-bob-y mt-4 z-n1 d-sm-block d-none">
</section>

{{-- Pakistan cities datalist — shared by all city inputs --}}
<datalist id="pak-cities">
    <option value="Lahore"><option value="Karachi"><option value="Islamabad">
    <option value="Rawalpindi"><option value="Faisalabad"><option value="Multan">
    <option value="Peshawar"><option value="Quetta"><option value="Sialkot">
    <option value="Gujranwala"><option value="Hyderabad"><option value="Bahawalpur">
    <option value="Sargodha"><option value="Sukkur"><option value="Abbottabad">
    <option value="Dera Ghazi Khan"><option value="Sheikhupura"><option value="Jhang">
    <option value="Rahim Yar Khan"><option value="Gujrat"><option value="Sahiwal">
    <option value="Mardan"><option value="Kasur"><option value="Dera Ismail Khan">
    <option value="Nawabshah"><option value="Mingora"><option value="Chiniot">
    <option value="Okara"><option value="Mandi Bahauddin"><option value="Jhelum">
    <option value="Khanewal"><option value="Mirpur Khas"><option value="Larkana">
    <option value="Muzaffarabad"><option value="Turbat"><option value="Kohat">
    <option value="Jacobabad"><option value="Shikarpur"><option value="Nowshera">
    <option value="Hafizabad"><option value="Attock"><option value="Wah Cantt">
    <option value="Muzaffargarh"><option value="Vehari"><option value="Pakpattan">
    <option value="Chakwal"><option value="Narowal"><option value="Bhakkar">
    <option value="Lodhran"><option value="Mansehra"><option value="Bannu">
    <option value="Haripur"><option value="Charsadda"><option value="Khuzdar">
    <option value="Hub"><option value="Gwadar"><option value="Sibi">
    <option value="Chaman"><option value="Loralai"><option value="Zhob">
    <option value="Mirpur AJK"><option value="Rawalakot"><option value="Gilgit">
    <option value="Skardu"><option value="Sadiqabad"><option value="Dadu">
    <option value="Sanghar"><option value="Khairpur"><option value="Tando Adam">
</datalist>
@endsection

@push('scripts')
<script>
$(function () {

    // ── Gift toggle ──────────────────────────────────────────────────────────
    var $giftCheckbox  = $('#gift-checkbox');
    var $giftValue     = $('#gift-value');
    var $sectionGift   = $('#section-gift');
    var $sectionReg    = $('#section-regular');

    function applyGiftToggle() {
        var isGift = $giftCheckbox.is(':checked');
        $giftValue.val(isGift ? 'Yes' : 'No');

        if (isGift) {
            $sectionReg.addClass('d-none');
            $sectionGift.removeClass('d-none');
            $sectionReg.find('input,select').prop('required', false);
            $sectionGift.find('input[name="gift_sender_name"], input[name="gift_sender_phone"], input[name="gift_receiver_name"], input[name="gift_receiver_phone"], textarea[name="gift_receiver_address"]').prop('required', true);
        } else {
            $sectionGift.addClass('d-none');
            $sectionReg.removeClass('d-none');
            $sectionGift.find('input,textarea').prop('required', false);
            $sectionReg.find('input[name="name"], input[name="email"], input[name="phone"], input[name="city"], input[name="address"]').prop('required', true);
        }
    }

    $giftCheckbox.on('change', applyGiftToggle);
    applyGiftToggle();

    // ── Payment method toggle ─────────────────────────────────────────────────
    function applyPaymentToggle() {
        $('.payment-details').hide().find('input[type="file"]').prop('required', false);
        var $sel = $('input[name="mode"]:checked');
        if ($sel.length) {
            var $det = $('#details-' + $sel.val());
            $det.show();
            $det.find('input[type="file"]').prop('required', true);
        }
    }

    $('input[name="mode"]').on('change', applyPaymentToggle);
    applyPaymentToggle();

    // ── Form submit validation ────────────────────────────────────────────────
    $('.billing-form').on('submit', function (e) {
        var mode = $('input[name="mode"]:checked').val();
        if (!mode) {
            e.preventDefault();
            alert('Please select a payment method before confirming your order.');
            return false;
        }
        if (mode === 'bank_transfer' && !$('#picture')[0].files.length) {
            e.preventDefault();
            alert('Please upload your bank payment receipt.');
            $('#picture')[0].scrollIntoView({ behavior: 'smooth' });
            return false;
        }
        if (mode === 'wallet' && !$('#wallet_picture')[0].files.length) {
            e.preventDefault();
            alert('Please upload your JazzCash transfer screenshot.');
            $('#wallet_picture')[0].scrollIntoView({ behavior: 'smooth' });
            return false;
        }
    });

});
</script>
@endpush
