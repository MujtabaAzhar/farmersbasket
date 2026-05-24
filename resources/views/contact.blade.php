@extends('layouts.app')
@section('content')
    <!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url(assets/img/hero/breadcrumb-banner.jpg);">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Contact page
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
                        Contact page
                    </li>
                </ul>
            </div>
        </div>
        <img src="assets/img/home-1/home-shape-start.png" alt="img" class="bread-shape-start position-absolute">
        <img src="assets/img/home-1/home-shape-end.png" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>

    <!-- contact hero start -->
    <section class="contact-section fix section-padding position-relative">
        <div class="container">
            <div class="row g-4">
                <div class="col-xl-5 col-lg-6">
                    <div class="wow fadeInUp" data-wow-delay="0.9s">
                        <div class="section-title-style mb-30">
                            <h2 class="wow fadeInUp mb-sm-3 fw-bold fs-32 mb-2 white-clr text-black lh-1 fw-semibold text-capitalize text-capitalize"
                                data-wow-delay=".4s">
                                Get In Touch
                            </h2>
                            <p class="fs-16 wow fadeInUp" data-wow-delay="0.6s">
                                Have a question, special request, or want to book an order?
                            </p>
                        </div>
                        @if (session('success'))
                            <div class="alert alert-success custom_alert">
                                {{ session('success') }}
                            </div>
                        @endif
                        <form class="billing-form reservation-form p-0 needs-validation" name="contact-us-form"
                            novalidate="" method="POST" action="{{ route('home.contact.store') }}">
                            @csrf
                            <div class="row g-lg-4 g-3">
                                <div class="col-sm-12">
                                    <div class="form-group m-0">
                                        <input type="text" placeholder="your Name" value="{{ old('name') }}"
                                            name="name" required>
                                        @error('name')
                                         <span class="text-danger custom_alert">{{ $message }}</span>
                                         @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0">
                                        <input type="text" placeholder=" Email" value="{{ old('email') }}" name="email" required>
                                        @error('email')
                                             <span class="text-danger custom_alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group m-0">
                                        <input type="text" placeholder="Phone" value="{{ old('phone') }}" name="phone" required>
                                        
                                        @error('phone')
                                           <span class="text-danger custom_alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                             
                                <div class="col-sm-12">
                                    <div class="form-group m-0">
                                        <textarea name="comment" rows="4" placeholder="Message">{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <span class="text-danger custom_alert">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-4">
                                    <button type="submit" class="theme-btn px-4 rounded-2">
                                        Send Us Message <i class="fa-solid fa-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-xl-7 col-lg-6">
                    <div class="ps-xl-4 d-flex flex-column gap-lg-4 gap-3">
                        <div class="card-effect contact-office-item d-flex align-items-sm-center align-items-start wow fadeInUp"
                            data-wow-delay="0.3s">
                            <div class="thumb w-100">
                                <img src="{{ asset('assets/img/inner/contact-office1.jpg') }}" alt="img" class="w-100">
                            </div>
                            <div class="content py-2 px-2">
                                <h4 class="text-black fw-semibold mb-1">United Bakers</h4>
                                <p class="fs-16 mb-md-3 mb-2">Bakery building, Multan Rd, Lodhrān, Pakistan</p>
                                <a href="https://maps.app.goo.gl/WgeJUhPuZofSHtrJ7" target="_blank"
                                    class="text-black fs-18 fw-semibold text-decoration-underline d-block mb-1">
                                    View on map
                                </a>
                                <a href="javascript:void(0)" class="d-flex text-call fs-16 fw-500 align-items-center gap-1">
                                    <i class="fa-solid fa-phone"></i> +92608362144
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <img src="assets/img/inner-global-pasta.png" alt="img"
            class="position-absolute top-40 end-0 float-bob-y z-n1 d-sm-block d-none">
        <img src="assets/img/inner-global-left.png" alt="img"
            class="position-absolute bottom-0 start-0 float-bob-y pt-5 z-n1 d-sm-block d-none">
    </section>
@endsection
