@extends('layouts.app')
@section('content')
  <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Shop
                </h2>
                <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                    <li>
                        <a href="{{ route('home.index')}}">
                            Home
                        </a>
                    </li>
                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        Shop
                    </li>
                </ul>
            </div>
        </div>
        <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="img" class="bread-shape-start position-absolute">
        <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>
    <!--- SHop Section -->
    <section class="shop-section position-relative z-1 fix section-padding">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-3">
                    <div class="shop-category cmn-shadow-shop mb-xxl-4 mb-3">
                        <h4 class="mb-3">Categories</h4>
                        <div class="d-flex flex-column gap-3 mb-3">
                            
                               @foreach ($categories as $category)
                            <div class="d-flex w-100 link-effect align-items-center justify-content-between border-bottom pb-3 form-check">
                                <label class="form-check-label d-flex align-items-center gap-1 fs-15 text-clr w-100" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input chk-category me-2" name="categories" value="{{$category->id}}" 
                                        {{ in_array($category->id, explode(',', $f_categories)) ? 'checked' : '' }}>
                                    <img src="assets/img/icons/shop-check.png" alt="img" class="d-none"> <!-- kept for icon continuity if needed -->
                                     {{ $category->name }}
                                </label>
                                <span class="fs-13 text-clr">{{ $category->products->count() }}</span>
                            </div>
                             @endforeach
                        </div>
                        <h4 class="mb-3">Brands</h4>
                        <div class="d-flex flex-column gap-3 mb-3">
                            
                                @foreach ($brands as $brand)
                            <div class="d-flex w-100 link-effect align-items-center justify-content-between border-bottom pb-3 form-check">
                                <label class="form-check-label d-flex align-items-center gap-1 fs-15 text-clr w-100" style="cursor: pointer;">
                                    <input type="checkbox" class="form-check-input chk-brand me-2" name="brands" value="{{$brand->id}}" 
                                        {{ in_array($brand->id, explode(',', $f_brands)) ? 'checked' : '' }}>
                                    <img src="assets/img/icons/shop-check.png" alt="img" class="d-none"> <!-- kept for icon continuity if needed -->
                                               {{ $brand->name }}
                                </label>
                                <span class="fs-13 text-clr">  {{ $brand->products->count() }}</span>
                            </div>
                             @endforeach
                        </div>

                          {{-- <div class="price-range-wrapper">
                            <div class="slider-container position-relative">
                                <input type="range" id="min-slider" name="min-slider" class="slider" min="1" max="10000"
                                    value="{{ $min_price }}">
                                <input type="range" id="max-slider" name="max-slider" class="slider" min="1" max="10000"
                                    value="{{ $max_price }}">
                            </div>
                            <div class="price-text pt-4 d-flex gap-3 align-items-center">
                                <label for="amount">Price:</label>
                                <span class="fs-15 text-clr">Rs <span id="min-val">{{ $min_price }}</span> - Rs <span id="max-val">{{ $max_price }}</span></span>
                            </div>
                        </div> --}}
                        <h4 class="mb-3">Filter By Price</h4>
                        <div class="price-range-wrapper">
                            <div class="slider-container">
                                <input type="range" id="min-slider" class="slider" min="130" max="10000"  name="price_range_min"
                                    value="{{ $min_price }}">
                                <input type="range" id="max-slider" class="slider" min="130" max="10000" name="price_range_max"
                                    value="{{ $max_price }}">
                            </div>
                            <div class="price-text pt-4 d-flex gap-3">
                                <label for="amount">Price:</label>
                                <input type="text" id="amount" readonly style="border:0;">
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-lg-9">
                    <div
                        class="shop-filter-area border rounded-2 py-3 px-3 d-flex align-items-center justify-content-between flex-wrap gap-3 mb-xxl-4 mb-3">
                        <span class="fs-15 text-clr">Showing 1 – {{$size}} of {{ $products->count() }} results</span>
                        <div class="d-flex align-items-center shop-filter-inner">
                            <select id="pagesize" name="pagesize">
                                 <option value="12" {{ $size == 12 ? 'selected' : '' }}>Show</option>
                            <option value="24" {{ $size == 24 ? 'selected' : '' }}>24</option>
                            <option value="48" {{ $size == 48 ? 'selected' : '' }}>48</option>
                            <option value="102" {{ $size == 102 ? 'selected' : '' }}>102</option>
                            </select>
                              <select id="orderby" name="orderby">
                               <option value="-1" {{ $order == -1 ? 'selected' : '' }}>Default</option>
                            <option value="1" {{ $order == 1 ? 'selected' : '' }}>Date:New->Old</option>
                            <option value="2" {{ $order == 2 ? 'selected' : '' }}>Date:Old->New</option>
                            <option value="3" {{ $order == 3 ? 'selected' : '' }}>Rs:Low->High</option>
                            <option value="4" {{ $order == 4 ? 'selected' : '' }}>Rs:High->Low</option>
                            </select>
                            <ul class="nav d-flex flex-nowrap align-items-center gap-3 nav-tabs border-0" id="myTab"
                                role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link p-0 border-0 active" id="home-tab" data-bs-toggle="tab"
                                        data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                        aria-selected="true">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path d="M8 1H1V8H8V1Z" stroke="#353844" stroke-width="1.4"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M19 1H12V8H19V1Z" stroke="#353844" stroke-width="1.4"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M19 12H12V19H19V12Z" stroke="#353844" stroke-width="1.4"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                            <path d="M8 12H1V19H8V12Z" stroke="#353844" stroke-width="1.4"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </li>
                              
                            </ul>
                        </div>
                    </div>
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="home" role="tabpanel"
                            aria-labelledby="home-tab">
                            <div class="row g-xxl-4 g-xl-3 g-2">
                                @foreach ($products as $product)

                                    <div class="col-sm-6 col-lg-4">
                                          <a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}"{{ $product->name }}"
                                                        class="text-black link-effect">
                                        <div class="restaurant-card rounded-4 overflow-hidden restaurant-card_text position-relative border card-scale h-100 rounded-12 wow fadeInUp"
                                            data-wow-delay="0.3s">
                                            <div class="thumb rounded-top-3 d-block position-relative">
                                                <img src="{{ asset('uploads/products/' . $product->image) }}"
                                                    alt="img" class="w-100">
                                            </div>
                                            <div class="position-absolute z-1 top-0 theme3-bg fs-12 py-1 lh-base ps-2 pe-3 text-white heading-font fw-500 d-inline-flex align-items-center gap-1"
                                                style="border-bottom-right-radius: 20px;">
                                                Sale
                                            </div>
                                            <div class="cont py-3 px-xxl-4 px-3 bg-white">
                                                <h6 class="mb-2">
                                                  {{ $product->name }}
                                                </h6>
                                                <div class="d-flex gap-2 align-items-center mb-2">
                                                    <div class="d-flex align-items-center gap-1 fs-14 text-clr">
                                                        <div
                                                            class="theme2-bg rounded-1 d-center fs-10 text-white w-16px h-16px">
                                                            <i class="fa-solid fa-star"></i>
                                                        </div> <span><span class="text-black">4.8</span>
                                                            {{ $product->short_description }}</span>
                                                    </div>
                                                </div>
                                                <p class="fs-12 mb-2 lh-18">
                                                    {{ $product->category->name }} <br> {{ $product->brand->name }}
                                                </p>
                                                <div class="d-flex align-items-center gap-sm-3 gap-2 flex-wrap mb-2">
                                                    <div class="d-flex align-items-center gap-1">
                                                        @if ($product->sale_price)
                                                            <del
                                                                class="fs-16 text4-clr">Rs {{ $product->regular_price }}</del>
                                                            <span
                                                                class="theme3-clr fw-semibold fs-16">Rs {{ $product->sale_price }}</span>
                                                        @else
                                                            <span class="theme3-clr fw-semibold fs-16">Rs
                                                                {{ $product->regular_price }}</span>
                                                        @endif
                                                     
                                                    </div>
                                                </div>
                                                  <div class="d-flex align-items-center gap-sm-3 gap-2 flex-wrap ">
                                                    <div class="d-flex align-items-center gap-1">
                                                      
                                                        @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                                                            <a href="{{ route('cart.index') }}"
                                                                class="theme-btn  heading-font rounded-pill py-2 px-3">
                                                                Go to Cart
                                                            </a>
                                                        @else
                                                            <form name="addtocart-form" method="post"
                                                                action="{{ route('cart.add') }}">
                                                                @csrf
                                                                <input type="hidden" name="id"
                                                                    value="{{ $product->id }}">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $product->name }}">
                                                                <input type="hidden" name="quantity" value="1">
                                                                <input type="hidden" name="price"
                                                                    value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">

                                                                <button type="submit"
                                                                    class="theme-btn  heading-font rounded-pill py-2 px-3">
                                                                    Add to Cart
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                                                            <form
                                                                action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}"
                                                                method="POST">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"  class="theme-btn btn-outline-theme heading-font rounded-pill py-2 px-3"
                                                                    title="Add To Wishlist">
                                                                    Remove Wishlist
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('wishlist.add') }}" method="POST" >
                                                                @csrf
                                                                <input type="hidden" name="id"
                                                                    value="{{ $product->id }}">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $product->name }}">
                                                                <input type="hidden" name="quantity" value="1">
                                                                <input type="hidden" name="price"
                                                                    value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                                                                <button type="submit"  class="theme-btn btn-outline-theme heading-font rounded-pill py-2 px-3"
                                                                    title="Add To Wishlist">
                                                                       Add to Wishlist
                                                                    {{-- <img src="{{ asset('assets/img/icon/wishlist_black.png') }}"
                                                                        alt="tolly-icon"> --}}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
</a>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        <ul class="pagination d-flex align-items-center gap-1 justify-content-center flex-wrap mt-40">
                            {{ $products->links('pagination::bootstrap-5') }}
                            {{-- <li>
                            <a href="shop-details.html"
                                class="fs-14 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg">
                                Previous Page
                            </a>
                        </li>
                        <li>
                            <a href="shop-details.html"
                                class="fs-17 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg active">
                                1
                            </a>
                        </li>
                        <li>
                            <a href="shop-details.html"
                                class="fs-17 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg">
                                2
                            </a>
                        </li>
                        <li>
                            <a href="shop-details.html"
                                class="fs-17 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg">
                                3
                            </a>
                        </li>
                        <li>
                            <a href="shop-details.html"
                                class="fs-17 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg">
                                4
                            </a>
                        </li>
                        <li>
                            <a href="shop-details.html"
                                class="fs-14 fw-semibold text-black py-2 px-3 text-center rounded-2 pagination-bg">
                                Next Page
                            </a>
                        </li> --}}
                        </ul>
                    </div>
                </div>
            </div>
            <img src="assets/img/inner-global-left.png" alt="img"
                class="position-absolute bottom-0 start-0 float-bob-y pt-100 mt-4 z-n1 d-sm-block d-none">
            <img src="assets/img/inner-global-chess.png" alt="img"
                class="position-absolute top-0 end-0 float-bob-y pt-100 mt-4 z-n1 d-sm-block d-none">
    </section>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <img src="assets/img/icons/f-chef2.png" alt="img">
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div class="thumb w-100 rounded-4">
                                <img src="assets/img/inner/t-details-1.jpg" alt="img" class="w-100 rounded-4">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="quick-view-content">
                                <h3 class="mb-2 fw-semibold">Chess Mashala</h3>
                                <p class="fs-16 text-clr mb-lg-4 mb-3">
                                    Reprehenderit quibusdam dignissimos assumenda, sapiente eos repudiandae quas tempora
                                    voluptate totam corrupti deleniti ipsa iste quo impedit dolorem ullam temporibus
                                    nisi eum.
                                </p>
                                <h5 class="fw-500 mb-1">Size</h5>
                                <div class="d-flex align-items-center gap-2 mb-lg-4 mb-3">
                                    <div class="size_available active w-40px h-40px rounded-circle d-center">
                                        S
                                    </div>
                                    <div class="size_available w-40px h-40px rounded-circle d-center">
                                        S
                                    </div>
                                    <div class="size_available w-40px h-40px rounded-circle d-center">
                                        S
                                    </div>
                                </div>
                                <div class="d-flex gap-lg-2 gap-1 mb-lg-4 mb-3">
                                    <div
                                        class="view-new-cart d-flex flex-column align-items-center align-items-center gap-2 py-lg-3 py-2 px-1">
                                        <a href="javascript:void(0)" class="thumb card-effect w-80px h-80px rounded-2">
                                            <img src="assets/img/inner/shop-grilled1.jpg" alt="img"
                                                class="w-100 rounded-2">
                                        </a>
                                        <div class="content">
                                            <a href="javascript:void(0)"
                                                class="fs-14 mb-1 fw-semibold text-black lh-1 d-block">Grilled
                                                Platter</a>
                                            <div class="fs-16 theme3-clr fw-bold">Rs 19.00</div>
                                        </div>
                                    </div>
                                    <div
                                        class="view-new-cart d-flex flex-column align-items-center align-items-center gap-2 py-lg-3 py-2 px-1">
                                        <a href="javascript:void(0)" class="thumb card-effect w-80px h-80px rounded-2">
                                            <img src="assets/img/inner/shop-grilled1.jpg" alt="img"
                                                class="w-100 rounded-2">
                                        </a>
                                        <div class="content">
                                            <a href="javascript:void(0)"
                                                class="fs-14 mb-1 fw-semibold text-black lh-1 d-block">Eggstasy
                                                Omelet</a>
                                            <div class="fs-16 theme3-clr fw-bold">Rs 14.00</div>
                                        </div>
                                    </div>
                                    <div
                                        class="view-new-cart d-flex flex-column align-items-center align-items-center gap-2 py-lg-3 py-2 px-1">
                                        <a href="javascript:void(0)" class="thumb card-effect w-80px h-80px rounded-2">
                                            <img src="assets/img/inner/shop-grilled1.jpg" alt="img"
                                                class="w-100 rounded-2">
                                        </a>
                                        <div class="content">
                                            <a href="javascript:void(0)"
                                                class="fs-14 mb-1 fw-semibold text-black lh-1 d-block">Scramble
                                                Shine</a>
                                            <div class="fs-16 theme3-clr fw-bold">Rs 36.00</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3 d-flex flex-wrap align-items-center gap-xxl-2 gap-1">
                                    <div class="quantity-wrapper d-inline-flex align-items-center">
                                        <button type="button" class="quantityDecrement">-</button>
                                        <input type="text" value="1" readonly>
                                        <button type="button" class="quantityIncrement">+</button>
                                    </div>
                                    <a href="cart-page.html" class="theme-btn fs-14 px-3 text-nowrap">
                                        Buy Now
                                    </a>
                                    <h3 class="fw-semibold mt-2 text-end px-3">Rs 541</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
      <form id="frmfilter" method="GET" action="{{ route('shop.index') }}">
        <input type="hidden" name="page" value="{{ $products->currentPage() }}" />
        <input type="hidden" name="size" id="size" value="{{ $size }}" />
        <input type="hidden" name="order" id="order" value="{{ $order }}" />
        <input type="hidden" name="brands" id="hdnBrands" />
        <input type="hidden" name="categories" id="hdnCategories" />
        <input type="hidden" name="min" id="hdnMinPrice" value="{{ $min_price }}" />
        <input type="hidden" name="max" id="hdnMaxPrice" value="{{ $max_price }}" />
    </form>
@endsection
@push('scripts')
    <script>
        $(function() {
            $('#pagesize').on('change', function() {
                $("#size").val($("#pagesize option:selected").val());
                $('#frmfilter').submit();
            });

            $('#orderby').on('change', function() {
                $("#order").val($("#orderby option:selected").val());
                $('#frmfilter').submit();
            });

            $("input[name='brands']").on('change', function() {
                var brands = "";
                $("input[name='brands']:checked").each(function() {
                    if (brands == "")
                        brands += $(this).val();
                    else
                        brands += "," + $(this).val();
                });
                $("#hdnBrands").val(brands);
                $('#frmfilter').submit();
            });

            $("input[name='categories']").on('change', function() {
                var categories = "";
                $("input[name='categories']:checked").each(function() {
                    if (categories == "")
                        categories += $(this).val();
                    else
                        categories += "," + $(this).val();
                });
                $("#hdnCategories").val(categories);
                $('#frmfilter').submit();
            });

            $("[name = 'price_range_min']").on('change', function() {
                var min = $(this).val();
            
                $("#hdnMinPrice").val(min);
                
                 $('#frmfilter').submit();
                
            });
             $("[name = 'price_range_max']").on('change', function() {
               
                var max = $(this).val();
                $("#hdnMaxPrice").val(max);
                 $('#frmfilter').submit();
                
            });
        });
    </script>
@endpush