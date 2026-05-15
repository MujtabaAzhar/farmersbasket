@extends('layouts.app')
@section('content')
    <!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                      Order Confirmation
                </h2>
                <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                    <li>
                        <a href="{{ route('home.index') }}">
                            Home
                        </a>
                    </li>

                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        Order Confirmation
                    </li>
                </ul>
            </div>
        </div>
        <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="img"
            class="bread-shape-start position-absolute">
        <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>

    <!--- SHop Section -->
    <section class="shop-section position-relative z-1 fix section-padding">
        <div class="container">

            <div class="row g-4">
                <div class="col-lg-12">
                     <div class="modal-content border-0 bg-transparent shadow-none">

            <!-- Thermal Receipt -->
            <div class="thermal-receipt mx-auto">

                <!-- Success Icon -->
                <!-- <div class="text-center mb-3 no-print">
                    <div class="success-check">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div> -->

                <!-- Store Info -->
                <div class="text-center receipt-header">
                    <h4>FARMER'S BASKET</h4>
                    <p>Fresh Mangoes & Bakery</p>
                    <p>Multan, Punjab, Pakistan</p>
                    <p>Phone: +92 300 1234567</p>
                </div>

                <div class="divider"></div>

                <!-- Order Info -->
                <div class="receipt-info">
                    <div class="d-flex justify-content-between">
                        <span>Order ID:</span>
                        <strong>#FB1001</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Tracking:</span>
                        <strong>MNG-88291</strong>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Date:</span>
                        <span>11-May-2026</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Payment:</span>
                        <span>COD</span>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Customer -->
                <div class="receipt-info mb-3">
                    <strong>Customer</strong>
                    <p class="mb-1">John Doe</p>
                    <p class="mb-1">+92 300 1234567</p>
                    <p class="mb-0">Multan, Punjab</p>
                </div>

                <div class="divider"></div>

                <!-- Products -->
                <div class="products-section">

                    <div class="product-row">
                        <div>
                            <strong>Chaunsa Mango Box</strong>
                            <small>2 x Rs. 2000</small>
                        </div>

                        <div class="text-end">
                            <strong>4000</strong>
                        </div>
                    </div>

                    <div class="product-row">
                        <div>
                            <strong>Langra Mango Box</strong>
                            <small>1 x Rs. 1800</small>
                        </div>

                        <div class="text-end">
                            <strong>1800</strong>
                        </div>
                    </div>

                </div>

                <div class="divider"></div>

                <!-- Totals -->
                <div class="receipt-total">

                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span>5800</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Delivery</span>
                        <span>200</span>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Discount</span>
                        <span>-300</span>
                    </div>

                    <div class="divider"></div>

                    <div class="d-flex justify-content-between grand-total">
                        <strong>TOTAL</strong>
                        <strong>Rs. 5700</strong>
                    </div>

                </div>

                <div class="divider"></div>

                <!-- Footer -->
                <div class="text-center receipt-footer">
                    <p>ORDER CONFIRMED</p>
                    <p>Fresh mangoes are being packed 🍋</p>
                    <p>Thank you for shopping!</p>

                    <div class="barcode">
                        || ||| |||| || |||||
                    </div>
                </div>

                <!-- Buttons -->
                <div class="text-center mt-4 no-print">

                    <button class="btn btn-dark px-4 py-2"
                            onclick="window.print()">
                        <i class="bi bi-printer me-2"></i>
                        Print Slip
                    </button>

                    <button class="btn btn-outline-dark px-4 py-2 ms-2"
                            data-bs-dismiss="modal">
                        Done
                    </button>

                </div>

            </div>
        </div>
                </div>

            </div>


        </div>
        <img src="assets/img/inner-global-pasta.png" alt="img"
            class="position-absolute bottom-0 pb-100 end-0 float-bob-y mt-4 z-n1 d-sm-block d-none">
    </section>
@endsection
