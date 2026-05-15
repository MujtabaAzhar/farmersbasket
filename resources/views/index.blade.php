




 
@extends('layouts.app')
@section('content')

    <!-- Hero section start -->
    <section class="hero-section hero-section-style02">
        <div class="swiper hero-slider position-relative">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-1">
                        <div class="hero-bg bg-cover"
                            style="background-image: url(assets/img/home-2/heor2-slide1.jpg);"></div>
                        <div class="container">
                            <div class="hero-content text-center">
                            <span class="sub-title fs-18 heading-font-cormorant text-white mb-sm-4 mb-3 d-block text-uppercase">
آموں کا شاہی موسم آ چکا ہے
</span>
                      <h1 class="white-clr heading-font-cormorant fw-bolder mb-xxl-4 mb-3">
پاکستان کے بہترین دیسی آم
</h1>
                             <p class="body-font text-white fs-16 mb-4 pb-2 d-block">
رسیلے چونسا، خوشبودار سندھڑی اور میٹھے انور رٹول — ہر آم قدرتی ذائقے اور تازگی سے بھرپور۔
</p>
                                <div class="text-center">
                                    <a href="{{ route('shop.index') }}"
                                        class="theme-btn btn-outline-white fw-normal text-capitalize gap-1">
                                        Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-1">
                        <div class="hero-bg bg-cover"
                            style="background-image: url(assets/img/home-2/heor2-slide2.jpg);"></div>
                        <div class="container">
                            <div class="hero-content text-center">
                                <span
                                    class="sub-title fs-18 heading-font-cormorant text-white mb-sm-4 mb-3 d-block text-uppercase">Mango Mania!</span>
                                <h1 class="white-clr heading-font-cormorant fw-bolder mb-xxl-4 mb-3">
                                   Discover Your New Favorite Flavor.
                                </h1>
                                <p class="body-font text-white fs-16 mb-4 pb-2 d-block">
                                  From sun-kissed orchards to your table, experience the unmatched sweetness of premium mangoes.  Each bite is a journey to paradise.
                                </p>
                                <div class="text-center">
                                    <a href="{{ route('shop.index') }}"
                                        class="theme-btn btn-outline-white fw-normal text-capitalize gap-1">
                                        Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="hero-1">
                        <div class="hero-bg bg-cover"
                            style="background-image: url(assets/img/home-2/heor2-slide3.jpg);"></div>
                        <div class="container">
                            <div class="hero-content text-center">
                                <span
                                    class="sub-title fs-18 heading-font-cormorant text-white mb-sm-4 mb-3 d-block text-uppercase">Juicy. Fresh. Mangoes.</span>
                                <h1 class="white-clr heading-font-cormorant fw-bolder mb-xxl-4 mb-3">
                                   Find Your Perfect Mango Match.
                                </h1>
                                <p class="body-font text-white fs-16 mb-4 pb-2 d-block">
                                    Taste the difference of naturally ripened mangoes.  Grown with care, packed with flavor, and guaranteed to delight your senses.
                                </p>
                                <div class="text-center">
                                    <a href="{{ route('shop.index') }}"
                                        class="theme-btn btn-outline-white fw-normal text-capitalize gap-1">
                                        Shop Now
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dots"></div>
        </div>
    </section>
    <!-- Menu Popular Products start -->
    <section class="menu-section-5 pt-100 fix">
        <div class="container">
            <div class="text-center gap-2 mb-30 pb-md-2 pb-2">
                <h3 class="wow fadeInUp white-clr text-black fs-30 lh-1 fw-semibold" data-wow-delay=".5s">
                    Popular Products
                </h3>
            </div>
            <div class="menu-section-wrap position-relative menu-bg">
                <div class="swiper menu-slid-wrap">
                    <div class="swiper-wrapper">
                        @foreach($fproducts as $fproduct)
                     
                        <div class="swiper-slide">
                            <div class="menu-component text-center d-flex flex-column gap-2">
                                <a href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}"
                                    class="card-split w-90px h-90px mx-auto rounded-circle d-center bg-white">
                                    <img width="60" height="60" src="{{ asset('uploads/products/' . $fproduct->image) }}" alt="{{ $fproduct->name }}"
                                        class="rounded-3">
                                </a>
                                <div>
                                    <h6 class="text-black pt-1"><a href="{{ route('shop.product.details', ['product_slug' => $fproduct->slug]) }}"
                                            class="text-black">{{ $fproduct->name }}</a></h6>
                                    <span class="fs-14">( {{ $fproduct->quantity }} )</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                   
                    </div>
                </div>
                <div
                    class="adjustment-arrow d-flex justify-content-lg-between justify-content-center mt-lg-0 mt-4 align-items-center gap-3">
                    <div class="arrow arrow-start d-center bg-white rounded-circle">
                        <button type="button" class="slide-btn5 rounded-circle d-center btn-outline-blak array-prev">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                    </div>
                    <div class="arrow arrow-end d-center bg-white rounded-circle">
                        <button type="button" class="slide-btn5 rounded-circle d-center btn-outline-blak array-next">
                            <i class="fa-solid fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Offer Section start -->
    <section class="offer-section5 position-relative pt-100 fix">
        <div class="container">
            <div class="row g-4">
                <div class="col-sm-6 col-lg-4">
                    <div class="offer-card fix h-100 rounded-4 position-relative wow fadeInUp" data-wow-delay="0.3s">
                        <a href="restaurant-details.html" class="w-100">
                            <img src="assets/img/home-1/offer-burger-thumb4.jpg" alt="icon" class="w-100">
                        </a>
                        {{-- <img src="assets/img/home-1/compo-11.png" alt="icon"
                            class="position-absolute start-0 float-bob-y" style="top: 200px;"> --}}
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="offer-card fix h-100 rounded-4 position-relative wow fadeInUp" data-wow-delay="0.3s">
                        <a href="restaurant-details.html" class="w-100">
                            <img src="assets/img/home-1/offer-burger-thumb5.jpg" alt="icon" class="w-100">
                        </a>
                        {{-- <img src="assets/img/home-1/compo-11.png" alt="icon" class="position-absolute"
                            style="top: 35%; left: 50%; transform: translateX(-50%);"> --}}
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4">
                    <div class="offer-card fix h-100 rounded-4 position-relative wow fadeInUp" data-wow-delay="0.3s">
                        <a href="restaurant-details.html" class="w-100">
                            <img src="assets/img/home-1/offer-burger-thumb6.jpg" alt="icon" class="w-100">
                        </a>
                        {{-- <img src="assets/img/home-1/price-badge15.png" alt="icon"
                            class="position-absolute top-0 start-0 mt-4 pt-2 px-4 float-bob-y"> --}}
                    </div>
                </div>
            </div>
        </div>
        <img src="assets/img/home-1/order-ele22.png" alt="img" class="position-absolute bottom-0 start-0 float-bob-y pt-100 mt-4 z-n1 d-sm-block d-none">
    </section>
    

    <!-- Super Delicious Deal Section start -->
    <section class="restaurant-section position-relative pt-100 pb-100 fix">
        <div class="container">
            <div class="d-flex align-items-end justify-content-between gap-3 flex-wrap mb-30 pb-xl-0 pb-2">
                <div class="section-title-style1">
                    <div class="d-flex flex-column gap-2">
                        <h3 class="wow fadeInUp white-clr text-black fs-30 lh-1 fw-semibold" data-wow-delay=".3s">
                            Super Delicious Deal
                        </h3>
                        <span class="w-32px section-badge1 style5"></span>
                    </div>
                </div>
                <a href="{{ route('shop.index')}}" class="theme-btn btn-outline-blak heading-font">
                    Show More <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
             <div class="row g-3 justify-content-center">
                @foreach ($sproducts as $product)
                  <div class="col-lg-6 col-md-6 col-xxl-4">
                    <div class="most-popular-card bg-white card-effect smooth d-flex align-items-xxl-center justify-content-between gap-2 border rounded-12 p-xl-4 p-3 wow fadeInUp"
                        data-wow-delay="0.3s">
                        <div class="cont">
                            <h6 class="mb-lg-1 mb-1"><a href="restaurant-details-2.html" class="link-effect">       {{ $product->name }}</a></h6>
                            <p class="fs-15 mb-lg-2 mb-1 max-w-200 lh-base">{{ $product->short_description }}</p>
                            <h6 class="theme3-clr fs-16 fw-bold">   
                                 @if ($product->sale_price)                           
                                 <del class="fs-16 text4-clr">Rs {{ $product->regular_price }}</del> Rs {{ $product->sale_price }}
                                  @else
                                   Rs {{ $product->sale_price }}
                                                            </h6>
                                                            @endif
                                                           
                                                         
                                                       
                                                        
                        </div>
                        <div class="thumb rounded-2 position-relative w-90px h-90px">
                            <img width="90" height="90" src="{{ asset('uploads/products/' . $product->image) }}" alt="img"
                                class="rounded-2">
                            <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}" 
                                class="w-28px h-28px z-1 position-absolute bottom-0 end-0 m-2 bg-white rounded d-center theme3-clr fs-14">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                  @endforeach
             
            </div>
        </div>
        
    </section>
    
    {{-- How to order  --}}
    <div class="how-order-section z-1 section-bg2 position-relative pb-80 pt-80 fix">
        <div class="container">
            <div class="section-title-style1 mx-auto mb-40 max-w-450 text-center">
                <h3 class="wow fadeInUp mb-sm-3 fw-bold mb-2 white-clr text-black fs-30 lh-1 fw-semibold text-capitalize text-capitalize"
                    data-wow-delay=".3s">
                    Our Seamless Process
                </h3>
                <p class="fs-16 wow fadeInUp" data-wow-delay="0.4s">Experience a streamlined journey from selection to delivery, ensuring quality at every touchpoint of your order.</p>
            </div>
            <div class="row g-4">
                <div class="col-sm-6 col-lg-4 wow fadeInUp" data-wow-delay="0.4s">
                    <div class="order-card_box order-card__theme3 icon-effect bg-white rounded-20 position-relative">
                        <div class="icons smooth rounded-circle w-80px h-80px d-center theme2-bg mb-sm-4 mb-3">
                            <img src="assets/img/icons/order-icon1.png" alt="img" class="icon">
                        </div>
                        <h4 class="mb-xl-2 mb-2"><a href="{{ route('shop.index') }}" class="link-effect">Browse Our Collection</a></h4>
                        <p class="fs-16">Explore our premium range of products curated for excellence. Select your items with confidence using our intuitive interface.</p>
                        <div
                            class="py-2 px-3 step-badge smooth fs-16 fw-semibold theme-clr rounded-start-pill position-absolute end-0 top-0 mt-lg-5 mt-md-4 mt-3">
                            Step-01
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 wow fadeInUp" data-wow-delay="0.6s">
                    <div class="order-card_box order-card__theme3 icon-effect bg-white rounded-20 position-relative">
                        <div class="icons smooth rounded-circle w-80px h-80px d-center theme2-bg mb-sm-4 mb-3">
                            <img src="assets/img/icons/order-icon2.png" alt="img" class="icon">
                        </div>
                        <h4 class="mb-xl-2 mb-2"><a href="{{ route('shop.index') }}" class="link-effect">Quality-Assured Packing</a>
                        </h4>
                        <p class="fs-16">Every order undergoes a rigorous quality check and professional packaging process to ensure your items arrive in pristine condition.</p>
                        <div
                            class="py-2 px-3 step-badge smooth fs-16 fw-semibold theme-clr rounded-start-pill position-absolute end-0 top-0 mt-lg-5 mt-md-4 mt-3">
                            Step-02
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-lg-4 wow fadeInUp" data-wow-delay="0.8s">
                    <div class="order-card_box order-card__theme3 icon-effect bg-white rounded-20 position-relative">
                        <div class="icons smooth rounded-circle w-80px h-80px d-center theme2-bg mb-sm-4 mb-3">
                            <img src="assets/img/icons/order-icon3.png" alt="img" class="icon">
                        </div>
                        <h4 class="mb-xl-2 mb-2"><a href="{{ route('shop.index') }}" class="link-effect">Efficient Doorstep Delivery</a></h4>
                        <p class="fs-16">Your package is dispatched via our reliable logistics network. Track your shipment in real-time as it makes its way to your location.</p>
                        <div
                            class="py-2 px-3 step-badge smooth fs-16 fw-semibold theme-clr rounded-start-pill position-absolute end-0 top-0 mt-lg-5 mt-md-4 mt-3">
                            Step-03
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img src="assets/img/home-1/order-ele1.png" alt="img" class="position-absolute z-n1 d-sm-block d-none"
            style="bottom: -20px; right: -50px;">
            <img src="assets/img/inner-global-left.png" alt="img" class="position-absolute bottom-0 start-0 float-bob-y pt-100 mt-4 z-n1 d-sm-block d-none">
    </div>

 @endsection