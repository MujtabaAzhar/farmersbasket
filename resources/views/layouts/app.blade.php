<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Farmers Basket') }}</title>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="author" content="Farmers Basket" />
    <link rel="shortcut icon" href="{{ asset('assets/img/logo/favicon.png') }}" type="image/x-icon">

    <!--<< Bootstrap min.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!--<< All Min Css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/all.min.css') }}">
    <!--<< Animate.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <!--<< Magnific Popup.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/magnific-popup.css') }}">
    <!--<< MeanMenu.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/meanmenu.css') }}">
    <!--<< Swiper Bundle.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/swiper-bundle.min.css') }}">
    <!--<< Nice Select.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/nice-select.css') }}">
    <!--<< Expose Font.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/expose.css') }}">
    <!--<< Main.css >>-->
    <link rel="stylesheet" href="{{ asset('assets/css/main.css') }}">

    <style>
      

.mango-text-item{
    color: #ffffff;
    font-size: 16px;
    font-weight: 700;
    white-space: nowrap;
    letter-spacing: 0.5px;
    padding-right: 40px;
    font-family: 'Poppins', sans-serif;
}


    </style>
    @stack("styles")
</head>
<body class="body-bg">

      <!-- Back To Top start -->
    <!-- <button id="back-top" class="back-to-top">
            <i class="fa-regular fa-arrow-up"></i>
        </button> -->

     <!-- Preloader Start -->
    <div id="preloader" class="preloader">
        <div class="animation-preloader">
            <div class="spinner">
            </div>
            <div class="txt-loading">
                <span data-text-preloader="F" class="letters-loading">
                    F
                </span>
                <span data-text-preloader="A" class="letters-loading">
                    A
                </span>
                <span data-text-preloader="R" class="letters-loading">
                    R
                </span>
                <span data-text-preloader="M" class="letters-loading">
                    M
                </span>
                <span data-text-preloader="E" class="letters-loading">
                    E
                </span>
                  <span data-text-preloader="'" class="letters-loading">
                    '
                </span>
                  <span data-text-preloader="S" class="letters-loading">
                    S
                </span>
            </div>
            <p class="text-center">Basket</p>
        </div>
        <div class="loader">
            <div class="row">
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-left">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
                <div class="col-3 loader-section section-right">
                    <div class="bg"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Offcanvas Area Start -->
    <div class="fix-area">
        <div class="offcanvas__info">
            <div class="offcanvas__wrapper">
                <div class="offcanvas__content">
                    <div class="offcanvas__top mb-4 d-flex justify-content-between align-items-center">
                        <div class="offcanvas__logo">
                            <a href="{{ route('home.index') }}">
                                <img src="{{ asset('assets/img/logo/logo-white.png') }}" alt="logo-img">
                            </a>
                        </div>
                        <div class="offcanvas__close">
                            <button>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mobile-menu fix mb-3"></div>
                    <div class="offcanvas__contact">
                        <div class="mb-4 d-xl-block d-none">
                            <h4 class="mb-2">Demo</h4>
                            <a href="{{ route('home.index') }}" class="w-75 d-center">
                                <img src="{{ asset('assets/img/hero/home1.jpg') }}" alt="img" class="w-100">
                            </a>
                        </div>
                        <!-- <h4>Contact Info</h4>  -->
                        <ul>
                            <li class="d-flex align-items-center">
                                <a href="{{ route('wishlist.index') }}" >Wishlist</a>
                            </li>
                            <li class="d-flex align-items-center">
                               <a href="#0" class="search-trigger" >Search</a>
                            </li>
                         
                        </ul>
                        <div class="header-button mt-4">
                            <a href="{{route('login')}}"
                                class="theme-btn d-inline-flex text-white justify-content-center align-items-center gap-xxl-2 gap-2 fs-16 rounded-1 fw-500 black-clr overflow-hidden">
                                Login
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                      
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas__overlay"></div>
    <!-- Top Scrolling Mango Text Bar -->
{{-- <section class="text-slider-section mango-marquee  theme-bg py-3">

    <div class="swiper sponsor-text-slide">

        <div class="swiper-wrapper align-items-center">

            <!-- Item -->
            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🥭 100% Farm Fresh Mangoes
                </div>
            </div>

            <!-- Item -->
            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    😋 Naturally Sweet & Juicy
                </div>
            </div>

            <!-- Item -->
            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🌱 Direct From Multan Farms
                </div>
            </div>

            <!-- Item -->
            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🚚 Premium Quality Delivered Fresh
                </div>
            </div>

            <!-- Repeat for Smooth Loop -->
            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🥭 100% Farm Fresh Mangoes
                </div>
            </div>

            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    😋 Naturally Sweet & Juicy
                </div>
            </div>

            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🌱 Direct From Multan Farms
                </div>
            </div>

            <div class="swiper-slide w-auto">
                <div class="mango-text-item">
                    🚚 Premium Quality Delivered Fresh
                </div>
            </div>

        </div>
    </div>
</section> --}}


    <!-- Header Section Start -->
    <header id="header-sticky" class="header-1 position-relative bg-white header-style03 header-style05">
        <div class="container">
            <div class="mega-menu-wrapper">
                <div class="header-main">
                    <div class="header-left">
                        <div class="logo">
                            <a href="{{ route('home.index') }}" class="header-logo">
                                <img src="{{ asset('assets/img/logo/log-black.png') }}" alt="logo-img">
                            </a>
                        </div>
                    </div>
                    <div class="mean__menu-wrapper">
                        <div class="main-menu">
                            <nav id="mobile-menu">
                                <ul>
                                     <li>
                                        <a href="{{ route('home.index') }}" >Home</a>
                                    </li>
                                      @auth
                                        @if(auth()->user()->utype === 'ADM' || auth()->user()->pos_role)
                                        <li>
                                            <a href="{{ route('pos.index') }}" >POS</a>
                                        </li>
                                        @endif
                                      @endauth
                                      <li>
                                        <a href="{{ route('shop.index') }}" >Shop</a>
                                    </li>
                                      <li>
                                        <a href="{{ route('home.order.tracking') }}" >Order Tracking</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('home.about') }}">About Us</a>
                                    </li>
                                    <li>
                                        <a href="{{ route('home.contact') }}">Contact</a>
                                    </li>
                                
                                </ul>
                            </nav>
                        </div>
                    </div>
                    <div class="header-right d-xl-none d-flex justify-content-end align-items-center gap-3">
                        <form action="{{ route('cart.index') }}" method="post">
                          <button type="submit" class="tolly-icon position-relative">
                            <img src="{{ asset('assets/img/icons/tolly.png') }}" alt="tolly-icon">
                             @if(Cart::instance('cart')->content()->count() > 0)
                            <span class="count-quan d-center count-quan-black text-white">{{Cart::instance('cart')->content()->count()}}</span>
                            @endif
                        </button>
                        </form>
                       
                        <div class="header__hamburger d-lg-none d-block my-auto">
                            <div
                                class="sidebar__toggle black-bg d-flex align-items-center justify-content-center w-40px h-40px rounded-circle sidebar__toggle fs-20 text-white">
                                <i class="fa-solid fa-bars"></i>
                            </div>
                        </div>
                    </div>
                    <div class="d-lg-flex d-none align-items-center gap-xxl-3 gap-3">
                        <a href="#0" class="search-trigger me-3">
                            <img src="{{ asset('assets/img/icon/search.png') }}" alt="img">
                        </a>
 @guest
 
                                 <a href="{{ route('login') }}" class="tolly-icon me-3">
                            <img src="{{ asset('assets/img/icon/user.png') }}" alt="img">
                        </a>
                          @else
                           <a href="{{Auth::user()->utype === 'ADM' ? route('admin.index') : route('user.index')}}" class="tolly-icon me-3">
                          <span style="font-family: Outfit, sans-serif;
    text-transform: capitalize;
    font-size: 16px;
    font-weight: 700;">{{Auth::user()->name}}</span>   <img src="{{ asset('assets/img/icon/user.png') }}" alt="img">
                        </a>
                           @endguest
                        <form action="{{ route('wishlist.index') }}" method="GET">
                        <button type="submit" class="tolly-icon position-relative">
                                <img src="{{ asset('assets/img/icon/wishlist.png') }}" alt="tolly-icon">
                                 @if(Cart::instance('wishlist')->content()->count() > 0)
                            <span class="count-quan d-center count-quan-black text-white">{{Cart::instance('wishlist')->content()->count()}}</span>  
                               @endif
                        </button>
                                     </form>
                     
                             <form action="{{ route('cart.index') }}" method="GET">
                        <button type="submit" class="tolly-icon position-relative">
                            <img src="{{ asset('assets/img/icons/tolly.png') }}" alt="tolly-icon">
                             @if(Cart::instance('cart')->content()->count() > 0)
                            <span class="count-quan d-center count-quan-black text-white">{{Cart::instance('cart')->content()->count()}}</span>
                            @endif
                        </button>
                                     </form>
                        <button type="button"
                            class="destop-bars black-bg w-40px h-40px rounded-circle d-xl-none d-flex align-items-center justify-content-center sidebar__toggle fs-20 text-white">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                      
                    </div>
                </div>
            </div>
        </div>
    </header>
 @yield('content')



    <div class="search-wrap">
        <div class="search-inner">
            <i class="fas fa-times search-close open" id="search-close"></i>
            <div class="search-cell">
                <form method="get">
                    <div class="search-field-holder">
                        <input type="search" class="main-search-input" placeholder="Search...">
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer section start -->
      <footer class="footer-section position-relative fix black-bg z-1">
        <img src="{{ asset('assets/img/inner/footer-shape-filter.png') }}" alt="img"
            class="position-absolute start-0 top-0 mt-5 pt-5 float-bob-y z-n1 d-lg-block d-none opacity-25">
        <img src="{{ asset('assets/img/home-4/footer4-right.png') }}" alt="img"
            class="position-absolute end-0 top-0 pt-100 mt-40 mx-5 float-bob-y z-n1 opacity-25">
        <div class="container">
            <div class="footer-widget-wrapper pb-80">
                <div class="d-flex align-items-center justify-content-between gap-3 pb-100">
                    <img src="{{ asset('assets/img/home-4/f-lin-dot.png') }}" alt="img" class="w-100 d-xl-block d-none">
                    <a href="{{ route('home.index') }}">
                        <img src="{{ asset('assets/img/logo/logo-white1.png') }}" alt="img">
                    </a>
                    <img src="{{ asset('assets/img/home-4/f-lin-dot.png') }}" alt="img" class="w-100 d-xl-block d-none">
                </div>
                <div class="row g-4">
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6 wow fadeInUp" data-wow-delay=".3s">
                        <div class="single-footer-widget py-0">
                            <h5 class="widget-head text-white text-uppercase fs-20 white-clr fw-semibold">
                                Help
                            </h5>
                            <ul class="important-link d-grid gap-sm-2 gap-1">
                                <li>
                                    <a href="order-traking.html"
                                        class="text-white opacity-75 fw-light link-effect"> <i class="fab fa-whatsapp"></i>
                                        Whatsapp</a>
                                </li>
                                 <li>
                                   <h5 class="text-white mb-lg-3 mb-2">+92608362144</h5>
                                </li>
                                  <li>
                                     <p class="fs-16 white-clr opacity-75">info@farmersbasket.com</p>
                                </li>
                                
                              
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6 wow fadeInUp" data-wow-delay=".4s">
                        <div class="single-footer-widget py-0">
                            <h5 class="widget-head text-white text-uppercase fs-20 white-clr fw-semibold">
                                about US
                            </h5>
                            <ul class="important-link d-grid gap-sm-2 gap-1">
                                <li>
                                    <a href="{{ route('home.about') }}" class="text-white opacity-75 fw-light link-effect">Our
                                        Story</a>
                                </li>
                                <li>
                                    <a href="{{ route('home.order.tracking') }}"
                                        class="text-white opacity-75 fw-light link-effect">Delivery</a>
                                </li>
                                <li>
                                    <a href="{{ route('home.contact') }}"
                                        class="text-white opacity-75 fw-light link-effect">Contact</a>
                                </li>
                               
                            </ul>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6 wow fadeInUp" data-wow-delay=".5s">
                        <div class="single-footer-widget py-0">
                            <h5 class="widget-head text-white text-uppercase fs-20 white-clr fw-semibold">
                                address
                            </h5>
                            <div class="footer-content">
                                <p class="fs-16 white-clr opacity-75 mb-xl-3 mb-2">
                                   Bakery building, Multan Rd, Lodhrān, Pakistan
                                </p>
                                <p> 
                                    <a href="https://maps.app.goo.gl/WgeJUhPuZofSHtrJ7" class="fs-16 white-clr opacity-75">Store
                                        Locator</a></p>
                               
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-5 col-sm-6 wow fadeInUp" data-wow-delay=".7s">
                        <div class="single-footer-widget py-0">
                            <h5 class="widget-head text-white text-uppercase fs-20 white-clr fw-semibold">
                                CONNECT
                            </h5>
                            <p class="fs-16 white-clr opacity-75 mb-xl-3 mb-2">
                               Loved the juicy sweetness and rich aroma! The mangoes were perfectly ripe.
                            </p>
                            {{-- <form action="#0" class="d-flex form-outline">
                                <input type="text" placeholder="Email Address">
                                <button type="button" class="btn p-0 border-0"><img
                                        src="{{ asset('assets/img/icons/arrow-right-long.png') }}" alt="icon"></button>
                            </form> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="container">
                <div
                    class="d-flex align-items-center justify-content-sm-between justify-content-center flex-wrap gap-2 fs-16 text-center">
                    <p class="fs-16 text4-clr">Copyright &copy; 2026 <a href="{{ route('home.index') }}" class="theme-clr">Farmer's Basket</a>
                        all Right
                        Reserved</p>
                    <div class="d-flex align-items-center flex-wrap gap-lg-4 gap-sm-3 gap-2">
                        <a href="contact.html" class="fs-16 text3-clr link-effect heading-font-afasad">Privacy
                            Policy</a>
                        <a href="contact.html" class="fs-16 text3-clr link-effect heading-font-afasad">Terms &
                            Conditions</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <!--<< All JS Plugins >>-->
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <!--<< Viewport Js >>-->
    <script src="{{ asset('assets/js/viewport.jquery.js') }}"></script>
    <!--<< Bootstrap Js >>-->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <!--<< Nice Select Js >>-->
    <script src="{{ asset('assets/js/jquery.nice-select.min.js') }}"></script>
    <!--<< Waypoints Js >>-->
    <script src="{{ asset('assets/js/jquery.waypoints.js') }}"></script>
    <!--<< Counterup Js >>-->
    <script src="{{ asset('assets/js/jquery.counterup.min.js') }}"></script>
    <!--<< Swiper Slider Js >>-->
    <script src="{{ asset('assets/js/swiper-bundle.min.js') }}"></script>
    <!--<< MeanMenu Js >>-->
    <script src="{{ asset('assets/js/jquery.meanmenu.min.js') }}"></script>
    <!--<< Magnific Popup Js >>-->
    <script src="{{ asset('assets/js/jquery.magnific-popup.min.js') }}"></script>
    <!--<< Wow Animation Js >>-->
    <script src="{{ asset('assets/js/wow.min.js') }}"></script>
    <!--<< Main.js >>-->
    <script src="{{ asset('assets/js/main.js') }}"></script>

  @stack("scripts")
</body>
</html>
