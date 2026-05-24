@extends('layouts.app')
@section('content')
    <!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url(assets/img/hero/breadcrumb-banner.jpg);">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Checkout
                </h2>
                <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                    <li>
                        <a href="index.html">
                            Home
                        </a>
                    </li>
                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('cart.index') }}">
                            Cart
                        </a>
                    </li>
                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        Checkout
                    </li>
                </ul>
            </div>
        </div>
        <img src="assets/img/home-1/home-shape-start.png" alt="img" class="bread-shape-start position-absolute">
        <img src="assets/img/home-1/home-shape-end.png" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>

    <!--- SHop Section -->
    <section class="shop-section position-relative z-1 fix section-padding">
        <div class="container">
            <form action="{{ route('cart.place.an.order') }}" method="POST" class="billing-form">
                @csrf
                <div class="row g-4">
                    <div class="col-lg-8">
                        <div class="checkout-billing-details h-100">
                            <h4 class="text-black mb-lg-4 mb-3 wow fadeInUp" data-wow-delay="fadeInUp0.2s">Billing Details
                            </h4>
                            <div class="form-group m-0 col-sm-12 mb-3">
                                <select name="gift" id="gift" class="" required>
                                    <option value="">Send as a gift?</option>
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <div class="row g-4">
                                <div id="gift-no" class="d-none" >
                                    <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <input type="hidden" class="form-control" name="locality" required=""
                                                value="Landmark">

                                            <input type="hidden" class="form-control" name="landmark" required=""
                                                value="No">

                                            <input type="hidden" class="form-control" name="zip" required=""
                                                value="66000">

                                            <input type="hidden" class="form-control" name="state" required=""
                                                value="state">

                                            <input type="hidden" class="form-control" name="country" required=""
                                                value="country">

                                            <input type="text" placeholder="Full Name" name="name" required=""
                                                value="{{ old('name') }}">
                                            @error('name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <input type="email" placeholder="E-mail" name="email" required=""
                                                value="{{ old('email') }}">
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <input type="text" placeholder="Phone" name="phone" required=""
                                                value="{{ old('phone') }}">
                                            @error('phone')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group mb-3">
                                            <select name="city" required="">
                                                <option value="">City</option>
                                                <option value="Multan">Multan</option>
                                                <option value="Lahore">Lahore</option>
                                                <option value="Karachi">Karachi</option>
                                            </select>
                                            @error('select')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-sm-12">
                                        <div class="form-group mb-3">
                                            <input type="text" placeholder="Address" name="address" required=""
                                                value="{{ old('address') }}">
                                                         @error('address')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                
</div>
                                <div id="gift-yes" class="d-none" >
                                    <div class="row">

                                        <div class="col-sm-6">
                                            <div class="card-body">
                                                <h5 class="card-title mb-4 text-success">FROM: Sender Details</h5>
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="senderName"
                                                        name="gift_sender_name" placeholder="Your full name"
                                                        value="{{ old('gift_sender_name') }}">
                                                    @error('gift_sender_name') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                                <div class="mb-3">
                                                    <input type="tel" class="form-control" id="senderPhone"
                                                        name="gift_sender_phone" placeholder="0300000000"
                                                        value="{{ old('gift_sender_phone') }}">
                                                    @error('gift_sender_phone') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                                @guest
                                                <div class="mb-3">
                                                    <input type="email" class="form-control" name="gift_sender_email"
                                                        placeholder="Your email address"
                                                        value="{{ old('gift_sender_email') }}">
                                                    @error('gift_sender_email') <span class="text-danger">{{ $message }}</span> @enderror
                                                </div>
                                                @endguest
                                                <div class="mb-3">
                                                    <input type="text" class="form-control" id="senderAddress"
                                                        name="gift_sender_address" placeholder="Your address (optional)"
                                                        value="{{ old('gift_sender_address') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <h5 class="card-title mb-4 text-warning">TO: Delivery Details</h5>
                                            <div class="mb-3">
                                                <input type="text" class="form-control" id="receiverName"
                                                    name="gift_receiver_name" placeholder="Recipient full name"
                                                    value="{{ old('gift_receiver_name') }}">
                                                @error('gift_receiver_name') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <input type="tel" class="form-control" id="receiverPhone"
                                                    name="gift_receiver_phone" placeholder="0300000000"
                                                    value="{{ old('gift_receiver_phone') }}">
                                                @error('gift_receiver_phone') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <input type="text" class="form-control" name="gift_receiver_city"
                                                    placeholder="City" value="{{ old('gift_receiver_city') }}">
                                            </div>
                                            <div class="mb-3">
                                                <textarea class="form-control" id="receiverAddress"
                                                    name="gift_receiver_address" rows="3"
                                                    placeholder="Full delivery address">{{ old('gift_receiver_address') }}</textarea>
                                                @error('gift_receiver_address') <span class="text-danger">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mb-3">
                                                <textarea class="form-control" name="gift_message" rows="2"
                                                    placeholder="Gift message (optional)">{{ old('gift_message') }}</textarea>
                                            </div>
                                        </div>

                                    </div>

                                </div>


                                <div class="payment-methods mt-4 mb-3">
                                    <h5 class="mb-3 border-bottom pb-2">Payment Methods</h5>

                                    <div class="form-check border rounded p-3 mb-3 bg-light d-flex align-items-start">
                                        <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="mode"
                                            value="counter" id="mode0" checked>
                                        <label class="form-check-label flex-grow-1" for="mode0">
                                            <span class="fs-16 fw-semibold text-dark d-block">Counter Payment</span>

                                            <!-- Collapsible Bank Details -->
                                            <div class="payment-details mt-2" id="details-counter"
                                                style="display: none;">
                                                <div class="text-muted">
                                                    <span class="fs-14 fw-normal">Pay at our store</span><br>
                                                </div>
                                                <div class="mt-3 pt-2 border-top">
                                                    <label for="picture"
                                                        class="form-label fs-14 fw-semibold text-dark mb-1">
                                                        Note<span class="text-danger"></span>
                                                    </label>
                                                    <input type="text" name="note" id="note"
                                                        class="form-control form-control-sm">

                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Method 1: Bank Transfer -->
                                    <div class="form-check border rounded p-3 mb-3 bg-light d-flex align-items-start">
                                        <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="mode"
                                            value="bank" id="mode1">
                                        <label class="form-check-label flex-grow-1" for="mode1">
                                            <span class="fs-16 fw-semibold text-dark d-block">Direct bank transfer</span>

                                            <!-- Collapsible Bank Details -->
                                            <div class="payment-details mt-2" id="details-bank" style="display: none;">
                                                <div class="text-muted">
                                                    <strong class="text-dark fs-15">Meezan Bank</strong><br>
                                                    <span class="fs-14 fw-normal">Title: Mujtaba Azhar</span><br>
                                                    <span class="fs-14 fw-normal">Acct No: 123456789</span><br>
                                                </div>
                                                <div class="mt-3 pt-2 border-top">
                                                    <label for="picture"
                                                        class="form-label fs-14 fw-semibold text-dark mb-1">
                                                        Upload Payment Receipt <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="file" name="picture" id="picture"
                                                        class="form-control form-control-sm">
                                                    <div class="invalid-feedback">Please upload your payment receipt to
                                                        proceed.</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Method 2: Mobile Wallets -->
                                    <div class="form-check border rounded p-3 mb-3 bg-light d-flex align-items-start">
                                        <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="mode"
                                            value="wallet" id="mode2">
                                        <label class="form-check-label flex-grow-1" for="mode2">
                                            <span class="fs-16 fw-semibold text-dark d-block">JazzCash / EasyPaisa</span>

                                            <!-- Collapsible Wallet Details -->
                                            <div class="payment-details mt-2" id="details-wallet" style="display: none;">
                                                <div class="row g-3 mt-1">
                                                    <div class="col-sm-6">
                                                        <div class="text-muted">
                                                            <strong class="text-dark fs-15">JazzCash</strong><br>
                                                            <span class="fs-14">Title: Mujtaba Azhar</span><br>
                                                            <span class="fs-14">Acct No: 123456789</span><br>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="text-muted">
                                                            <strong class="text-dark fs-15">EasyPaisa</strong><br>
                                                            <span class="fs-14">Title: Mujtaba Azhar</span><br>
                                                            <span class="fs-14">Acct No: 123456789</span><br>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 pt-2 border-top">
                                                    <label for="wallet_picture"
                                                        class="form-label fs-14 fw-semibold text-dark mb-1">
                                                        Upload Transfer Screenshot <span class="text-danger">*</span>
                                                    </label>
                                                    <input type="file" name="wallet_picture" id="wallet_picture"
                                                        class="form-control form-control-sm">
                                                    <div class="invalid-feedback">Please upload your wallet transaction
                                                        screenshot to proceed.</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Method 3: Cash on Delivery -->
                                    <div class="form-check border rounded p-3 bg-light d-flex align-items-start">
                                        <input class="form-check-input ms-0 me-3 mt-1" type="radio" name="mode"
                                            value="cod" id="mode3">
                                        <label class="form-check-label flex-grow-1" for="mode3">
                                            <span class="fs-16 fw-semibold text-dark d-block">Cash on Delivery</span>
                                        </label>
                                    </div>

                                    <small class="d-block mt-2 fs-12">Note: Make your payment directly into our bank
                                        account. Please use your Order ID as the payment reference. Your order will not be
                                        shipped until the funds have cleared in our account.</small>
                                </div>

                            </div>

                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="shadow-cus d-flex flex-column justify-content-between coupon-group position-relative h-100 p-xl-4 p-3 rounded-3 bg-white wow fadeInDown"
                            data-wow-delay="6.s">
                            <div class="div">
                                <h5 class="border-bottom pb-2 mb-3">Order Summary</h5>
                                <div class="d-flex flex-column gap-3 align-items-center pb-4">

                                    @foreach (Cart::instance('cart')->content() as $item)
                                        <div
                                            class="order-summary d-flex justify-content-between w-100 gap-2 border-bottom pb-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div>
                                                    <h5 class="text-black fs-14 lh-1 max-w-180 fw-500">{{ $item->name }}
                                                        x
                                                        {{ $item->qty }}</h5>

                                                </div>
                                            </div>
                                            <div class=" d-inline-flex align-items-center">
                                                <span class="fw-semibold theme-clr fs-14"> Rs
                                                    {{ $item->subtotal() }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="order-summary-footer">
                                @if (Session::has('discounts'))
                                    <div class="d-flex flex-column">
                                        <div
                                            class="d-flex align-items-center justify-content-between border-top pt-2 pb-1">
                                            <span class="fs-12 text-black fw-medium">Subtotal</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('discounts')['subtotal'] }}</span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between pb-2">
                                            <span
                                                class="fs-12 text-black fw-medium">{{ Session::get('coupon')['code'] }}</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('discounts')['discount'] }}</span>
                                        </div>
                                        <div
                                            class="d-flex align-items-center justify-content-between border-top pt-2 pb-3">
                                            <span class="fs-12 text-black fw-medium">Shipping</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('discounts')['shipping'] }}</span>
                                        </div>
                                        <div
                                            class="d-flex align-items-center justify-content-between border-top pt-2 pb-3">
                                            <span class="fs-12 text-black fw-medium">Total</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('discounts')['total'] }}</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="d-flex flex-column">
                                        <div
                                            class="d-flex align-items-center justify-content-between border-top pt-2 pb-1">
                                            <span class="fs-12 text-black fw-medium">Subtotal</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('checkout')['subtotal'] ?? Cart::instance('cart')->subtotal() }}</span>
                                        </div>
                                        @if (Session::get('checkout')['discount'] > 0)
                                            <div class="d-flex align-items-center justify-content-between pb-2">
                                                <span class="fs-12 text-black fw-medium">Discount</span>
                                                <span class="fs-12 text-black fw-medium">Rs
                                                    {{ Session::get('checkout')['discount'] }}</span>
                                            </div>
                                        @endif
                                        @if (Session::get('checkout')['tax'] > 0)
                                            <div class="d-flex align-items-center justify-content-between pb-2">
                                                <span class="fs-12 text-black fw-medium">Tax</span>
                                                <span class="fs-12 text-black fw-medium">Rs
                                                    {{ Session::get('checkout')['tax'] }}</span>
                                            </div>
                                        @endif
                                        <div class="d-flex align-items-center justify-content-between pb-2">
                                            <span class="fs-12 text-black fw-medium">Shipping</span>
                                            <span class="fs-12 text-black fw-medium">Rs
                                                {{ Session::get('checkout')['shipping'] ?? 0 }}</span>
                                        </div>
                                        <div
                                            class="d-flex align-items-center justify-content-between border-top pt-2 pb-3">
                                            <span class="fs-12 text-black fw-medium"><strong>Total</strong></span>
                                            <span class="fs-12 text-black fw-medium"><strong>Rs
                                                    {{ Session::get('checkout')['total'] ?? Cart::instance('cart')->total() }}</strong></span>
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
@endsection
@push('scripts')
    <script>
        $(function() {
            // Gift toggle functionality
            const $giftSelect = $('#gift');
            const $giftYesDiv = $('#gift-yes');
            const $giftNoDiv = $('#gift-no');

            function toggleGiftOptions() {
                const selectedValue = $giftSelect.val();
                
                if (selectedValue === 'Yes') {
                    $giftYesDiv.removeClass('d-none').show();
                    $giftNoDiv.addClass('d-none').hide();
                    
                    // Set required attribute on gift_yes inputs
                    $giftYesDiv.find('input, select').prop('required', true);
                    console.log('Yes selected - showing gift details');
                } else if (selectedValue === 'No') {
                    $giftYesDiv.addClass('d-none').hide();
                    $giftNoDiv.removeClass('d-none').show();
                    
                    // Remove required attribute from gift_yes inputs
                    $giftYesDiv.find('input, select').prop('required', false);
                    console.log('No selected - showing delivery details');
                } else {
                    $giftYesDiv.addClass('d-none').hide();
                    $giftNoDiv.addClass('d-none').hide();
                    
                    // Remove required attribute from gift_yes inputs
                    $giftYesDiv.find('input, select').prop('required', false);
                    console.log('No selection - hiding both');
                }
            }

            // Listen for change event
            $giftSelect.on('change', function() {
                toggleGiftOptions();
            });

            // Initialize on page load
            toggleGiftOptions();

            // Payment method toggle functionality
            const radioButtons = $('input[name="mode"]');
            const detailsSections = $('.payment-details');

            function togglePaymentDetails() {
                // Hide all detail blocks and strip their inputs of 'required' validation
                detailsSections.each(function() {
                    $(this).hide();
                    $(this).find('input[type="file"]').prop('required', false);
                });

                // Find the selected option
                const selectedRadio = $('input[name="mode"]:checked');
                if (selectedRadio.length) {
                    // Target the specific wrapper container via ID matching: "details-[value]"
                    const targetDetails = $('#details-' + selectedRadio.val());
                    if (targetDetails.length) {
                        targetDetails.show();
                        // Only make the file input required if its block is visible
                        targetDetails.find('input[type="file"]').prop('required', true);
                    }
                }
            }

            // Attach click events to all options
            radioButtons.on('change', function() {
                togglePaymentDetails();
            });

            // Run once on load to catch pre-checked defaults
            togglePaymentDetails();
        });
    </script>
@endpush
