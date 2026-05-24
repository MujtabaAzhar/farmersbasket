@extends('layouts.app')
@section('content')
  <!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url(assets/img/hero/breadcrumb-banner.jpg);">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    About us
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
                        About
                    </li>
                </ul>
            </div>
        </div>
        <img src="assets/img/home-1/home-shape-start.png" alt="img" class="bread-shape-start position-absolute">
        <img src="assets/img/home-1/home-shape-end.png" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>

    <!-- About section start -->
    <section class="about-section position-relative section-padding fix">
        <div class="container">
            <div class="about-wrapper">
                <div class="row g-xxl-5 g-4 align-items-lg-center">
                    <div class="col-lg-4 col-md-4">
                        <div class="thumb img-hover wow fadeInUp" data-wow-delay="0.2s">
                            <img src="assets/img/inner/about-m-thumb-big.png" alt="img"
                                style="border-radius: 100px 20px 20px 20px;" class="w-100">
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="d-flex align-items-center gap-3 justify-content-between mb-40">
                            <div class="section-title-style1 section-title2 max-w-450">
                                <h6 class="heading-font-cormorant text-uppercase fw-bold text-dark mb-lg-3 mb-2">Quality
                                    service</h6>
                                <h2 class="wow fadeInUp heading-font-cormorant mb-sm-3 fw-bolder mb-2 white-clr text-black lh-1 fw-semibold text-capitalize text-capitalize"
                                    data-wow-delay=".3s">
                                    About Our Restaurant
                                </h2>
                                <p class="fs-16 body-font wow fadeInUp" data-wow-delay=".5s">where tradition meets
                                    tastWe bring authentic ingredients
                                    and heartfelt hospitality to your table. Whether you're
                                    meal or a special occasio make every.</p>
                            </div>
                            <img src="assets/img/home-2/about1-dine.png" alt="icon" class="d-lg-block d-none">
                        </div>
                        <div class="row align-items-center g-lg-4 g-3">
                            <div class="col-lg-6">
                                <h4 class="fw-bolder text-dark mb-1 heading-font-cormorant">Opening Hours</h4>
                                <p class="fs-16 mb-xl-3 mb-2 body-font wow fadeInUp" data-wow-delay=".5s">meets tastWe
                                    bring authentic aera flavors
                                    fresh ingredients heartfelt.</p>
                                <h6 class="text-dark body-font fs-16 mb-30">Mon-Fri: 9 AM – 22 PM <br> Saturday: 9 AM –
                                    23 PM </h6>
                                <a href="about.html" class="theme-btn rounded-1 theme-opacity-10 text-uppercase fs-13 ">
                                    <span class="theme-clr">Contact With Us</span>
                                </a>
                            </div>
                            <div class="col-sm-6">
                                <div class="thumb img-hover max-w-350 ms-auto"
                                    style="border-radius: 20px 20px 100px 20px;">
                                    <img src="assets/img/inner/about-m-thumb-small.jpg" alt="img" class="w-100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How to Order section start -->
    <div class="how-order-section z-1 section-bg2 position-relative section-padding fix">
        <div class="container">
            <div
                class="d-flex flex-md-nowrap flex-wrap align-items-center gap-xl-2 gap-3 justify-content-between order-customize-wrapper">
                <div class="max-w-300">
                    <div class="order-card_box style2 icon-effect rounded-20 position-relative">
                        <div class="icons smooth rounded-4 w-72px h-72px d-center mb-sm-4 mb-3">
                            <img src="assets/img/icons/f-chef1.png" alt="img" class="icon">
                        </div>
                        <h3 class="mb-xl-2 mb-2 heading-font-cormorant"><a href="shop-details.html"
                                class="link-effect heading-font-cormorant fw-bolder">Professional Chef</a></h3>
                        <p class="fs-16">Mauris rhoncus aenean vellit scelerue
                            mauris pellentesque pulvinar.</p>
                    </div>
                </div>
                <div class="line"></div>
                <div class="max-w-300">
                    <div class="order-card_box style2 icon-effect rounded-20 position-relative">
                        <div class="icons smooth rounded-4 w-72px h-72px d-center mb-sm-4 mb-3">
                            <img src="assets/img/icons/f-chef2.png" alt="img" class="icon">
                        </div>
                        <h3 class="mb-xl-2 mb-2 heading-font-cormorant"><a href="shop-details.html"
                                class="link-effect heading-font-cormorant fw-bolder">Delicious Meals</a></h3>
                        <p class="fs-16">Mauris rhoncus aenean vellit scelerue
                            mauris pellentesque pulvinar.</p>
                    </div>
                </div>
                <div class="line"></div>
                <div class="max-w-300">
                    <div class="order-card_box style2 icon-effect rounded-20 position-relative">
                        <div class="icons smooth rounded-4 w-72px h-72px d-center mb-sm-4 mb-3">
                            <img src="assets/img/icons/f-chef3.png" alt="img" class="icon">
                        </div>
                        <h3 class="mb-xl-2 mb-2 heading-font-cormorant"><a href="shop-details.html"
                                class="link-effect heading-font-cormorant fw-bolder">Millions of Customer</a></h3>
                        <p class="fs-16">Mauris rhoncus aenean vellit scelerue
                            mauris pellentesque pulvinar.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--- Chef Section -->
    <section class="chef-section section-padding bg-cover fix"
        style="background-image: url(assets/img/home-2/chefs-bg.jpg);">
        <div class="container">
            <div class="section-title-style1 section-title2 mx-auto mb-40 text-center">
                <h6 class="heading-font text-uppercase fw-normal letter-spacing-1 text-white mb-lg-3 mb-2">Experts Where
                    Need</h6>
                <h2 class="wow fadeInUp heading-font mb-sm-3 fw-bolder mb-2 text-white lh-1 fw-semibold text-capitalize text-capitalize"
                    data-wow-delay=".3s">
                    Meet Our Skilled Chefs
                </h2>
                <p class="fs-16 text5-clr body-font wow fadeInUp" data-wow-delay=".5s">where tradition meets tastWe
                    bring authentic flavors area test ingredients</p>
            </div>
            <div class="row g-xl-4 g-3">
                <div class="col-sm-6 col-lg-3">
                    <div class="chef-single-item card-effect position-relative rounded-16 overflow-hidden wow fadeInUp"
                        data-wow-delay=".2s">
                        <div class="thumb d-block w-100 overflow-hidden">
                            <img src="assets/img/home-2/chef_01.jpg" alt="img" class="w-100 overflow-hidden">
                        </div>
                        <div class="content position-absolute bottom-0 z-1 bg-white rounded-2 px-lg-3 px-3 pb-lg-3 pb-3 mb-3 text-center pt-0"
                            style="width: 90%; left: 50%; transform: translateX(-50%);">
                            <div class="d-inline-flex align-items-center justify-content-center gap-2 theme-bg rounded-2 py-1 px-3 position-relative"
                                style="top: -15px;">
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-facebook-f fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-twitter fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-instagram fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-linkedin fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-whatsapp fs-18 text-white"></i>
                                </a>
                            </div>
                            <div>
                                <h5 class="heading-font-cormorant mb-lg-1">
                                    <a href="team-details.html"
                                        class="fw-semibold lh-1 fw-bolder heading-font-cormorant">
                                        Jenny Wilson
                                    </a>
                                </h5>
                                <span class="body-font fs-14 d-block">Chef Director</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="chef-single-item card-effect position-relative rounded-16 overflow-hidden wow fadeInUp"
                        data-wow-delay=".4s">
                        <div class="thumb d-block w-100 overflow-hidden">
                            <img src="assets/img/home-2/chef_02.jpg" alt="img" class="w-100 overflow-hidden">
                        </div>
                        <div class="content position-absolute bottom-0 z-1 bg-white rounded-2 px-lg-3 px-3 pb-lg-3 pb-3 mb-3 text-center pt-0"
                            style="width: 90%; left: 50%; transform: translateX(-50%);">
                            <div class="d-inline-flex align-items-center justify-content-center gap-2 theme-bg rounded-2 py-1 px-3 position-relative"
                                style="top: -15px;">
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-facebook-f fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-twitter fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-instagram fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-linkedin fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-whatsapp fs-18 text-white"></i>
                                </a>
                            </div>
                            <div>
                                <h5 class="heading-font-cormorant mb-lg-1">
                                    <a href="team-details.html"
                                        class="fw-semibold lh-1 fw-bolder heading-font-cormorant">
                                        De Enjoy
                                    </a>
                                </h5>
                                <span class="body-font fs-14 d-block">Chef Director</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="chef-single-item card-effect position-relative rounded-16 overflow-hidden wow fadeInUp"
                        data-wow-delay=".6s">
                        <div class="thumb d-block w-100 overflow-hidden">
                            <img src="assets/img/home-2/chef_03.jpg" alt="img" class="w-100 overflow-hidden">
                        </div>
                        <div class="content position-absolute bottom-0 z-1 bg-white rounded-2 px-lg-3 px-3 pb-lg-3 pb-3 mb-3 text-center pt-0"
                            style="width: 90%; left: 50%; transform: translateX(-50%);">
                            <div class="d-inline-flex align-items-center justify-content-center gap-2 theme-bg rounded-2 py-1 px-3 position-relative"
                                style="top: -15px;">
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-facebook-f fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-twitter fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-instagram fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-linkedin fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-whatsapp fs-18 text-white"></i>
                                </a>
                            </div>
                            <div>
                                <h5 class="heading-font-cormorant mb-lg-1">
                                    <a href="team-details.html"
                                        class="fw-semibold lh-1 fw-bolder heading-font-cormorant">
                                        Devid Lue
                                    </a>
                                </h5>
                                <span class="body-font fs-14 d-block">Chef Director</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-3">
                    <div class="chef-single-item card-effect position-relative rounded-16 overflow-hidden wow fadeInUp"
                        data-wow-delay=".8s">
                        <div class="thumb d-block w-100 overflow-hidden">
                            <img src="assets/img/home-2/chef_04.jpg" alt="img" class="w-100 overflow-hidden">
                        </div>
                        <div class="content position-absolute bottom-0 z-1 bg-white rounded-2 px-lg-3 px-3 pb-lg-3 pb-3 mb-3 text-center pt-0"
                            style="width: 90%; left: 50%; transform: translateX(-50%);">
                            <div class="d-inline-flex align-items-center justify-content-center gap-2 theme-bg rounded-2 py-1 px-3 position-relative"
                                style="top: -15px;">
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-facebook-f fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-twitter fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-instagram fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-linkedin fs-18 text-white"></i>
                                </a>
                                <a href="javascript:void(0)">
                                    <i class="fa-brands fa-whatsapp fs-18 text-white"></i>
                                </a>
                            </div>
                            <div>
                                <h5 class="heading-font-cormorant mb-lg-1">
                                    <a href="team-details.html"
                                        class="fw-semibold lh-1 fw-bolder heading-font-cormorant">
                                        Kon Joy
                                    </a>
                                </h5>
                                <span class="body-font fs-14 d-block">Chef Director</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Text Slide start -->
    <section class="text-slider-section theme-bg py-3">
        <div class="sponsor-text-slide swiper">
            <div class="swiper-wrapper">
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text1.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text-slide-flower.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text2.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text-slide-flower.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text3.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text-slide-flower.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text4.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text-slide-flower.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text5.png" alt="img">
                    </div>
                </div>
                <div class="swiper-slide w-fit">
                    <div class="text-slide-item rounded-3">
                        <img src="assets/img/home-2/text-slide-flower.png" alt="img">
                    </div>
                </div>
            </div>
        </div>
    </section>

  

    <div class="client-section z-1 position-relative pb-100 pt-80 fix">
        <div class="container">
            <div class="section-title-style1 mx-auto mb-40 max-w-450 text-center">
                <h3 class="wow fadeInUp text-black fs-30 lh-1 fw-semibold" data-wow-delay=".3s">
                    What’s Client Think About
                    Our Services
                </h3>
            </div>
            <div class="row g-4 align-items-center">
                <div class="col-lg-9">
                    <div class="testimonial-wrapper1">
                        <img src="assets/img/flow-theme3.png" alt="img" class="position-absolute"
                            style="top: -50px; left: -50px;">
                        <div class="swiper testimonial-slider">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div
                                        class="testimonial-items style1 d-flex flex-md-nowrap flex-wrap align-items-center position-relative">
                                        <div class="testimonial-thumb rounded-20 position-relative">
                                            <img src="assets/img/client-admin1.jpg" alt="img" class="rounded-20">
                                        </div>
                                        <div class="content">
                                            <div class="d-flex gap-1 mb-2">
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                            </div>
                                            <h3 class="text-black mb-2 fw-semibold">Great Product! Highly Recommended!
                                            </h3>
                                            <p class="mb-lg-3 mb-2 fs-16 max-w-480">“ Great Quality Products WitheryGood
                                                awrPackaging unknown
                                                printer took a galle rambled it make pecimive centuries
                                                Delicious Food Context ”</p>
                                            <div class=" gap-3 d-flex align-items-center">
                                                <img width="50" height="50" src="assets/img/admin1.jpg" alt="img"
                                                    class="rounded-circle">
                                                <div class="">
                                                    <h6 class="mb-0 fw-bold black-clr">
                                                        Annette Black
                                                    </h6>
                                                    <span class="fs-14 fw-500 pra-clr d-block">Sr.Designer</span>
                                                </div>
                                            </div>
                                        </div>
                                        <img src="assets/img/quote-white.png" alt="img" class="quote-icon">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div
                                        class="testimonial-items style1 d-flex flex-md-nowrap flex-wrap align-items-center position-relative">
                                        <div class="testimonial-thumb rounded-20 position-relative">
                                            <img src="assets/img/client-admin1.jpg" alt="img" class="rounded-20">
                                        </div>
                                        <div class="content">
                                            <div class="d-flex gap-1 mb-2">
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                            </div>
                                            <h3 class="text-black mb-2 fw-semibold">Great Product! Highly Recommended!
                                            </h3>
                                            <p class="mb-lg-3 mb-2 fs-16 max-w-480">“ Great Quality Products WitheryGood
                                                awrPackaging unknown
                                                printer took a galle rambled it make pecimive centuries
                                                Delicious Food Context ”</p>
                                            <div class=" gap-3 d-flex align-items-center">
                                                <img width="50" height="50" src="assets/img/admin1.jpg" alt="img"
                                                    class="rounded-circle">
                                                <div class="">
                                                    <h6 class="mb-0 fw-bold black-clr">
                                                        Annette Black
                                                    </h6>
                                                    <span class="fs-14 fw-500 pra-clr d-block">Sr.Designer</span>
                                                </div>
                                            </div>
                                        </div>
                                        <img src="assets/img/quote-white.png" alt="img" class="quote-icon">
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div
                                        class="testimonial-items style1 d-flex flex-md-nowrap flex-wrap align-items-center position-relative">
                                        <div class="testimonial-thumb rounded-20 position-relative">
                                            <img src="assets/img/client-admin1.jpg" alt="img" class="rounded-20">
                                        </div>
                                        <div class="content">
                                            <div class="d-flex gap-1 mb-2">
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                            </div>
                                            <h3 class="text-black mb-2 fw-semibold">Great Product! Highly Recommended!
                                            </h3>
                                            <p class="mb-lg-3 mb-2 fs-16 max-w-480">“ Great Quality Products WitheryGood
                                                awrPackaging unknown
                                                printer took a galle rambled it make pecimive centuries
                                                Delicious Food Context ”</p>
                                            <div class=" gap-3 d-flex align-items-center">
                                                <img width="50" height="50" src="assets/img/admin1.jpg" alt="img"
                                                    class="rounded-circle">
                                                <div class="">
                                                    <h6 class="mb-0 fw-bold black-clr">
                                                        Annette Black
                                                    </h6>
                                                    <span class="fs-14 fw-500 pra-clr d-block">Sr.Designer</span>
                                                </div>
                                            </div>
                                        </div>
                                        <img src="assets/img/quote-white.png" alt="img" class="quote-icon">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="swiper-dot dot-theme3"></div>
                    </div>
                </div>
                <div class="col-lg-3 mt-lg-0 mt-4">
                    <div class="theme3-bg text-center p-30 rounded-12 position-relative">
                        <img src="assets/img/satisfied-like.png" alt="icon" class="mb-30 pb-1">
                        <h5 class="mb-xl-2 text-white mb-1 fw-500">Satisfied Clients</h5>
                        <h1 class="text-white fw-semibold">100%</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
