@extends('layouts.app')
@section('content')
   <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Product Details
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
                        <a href="{{ route('shop.index')}}">
                            Shop
                        </a>
                    </li>
                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        Product Details
                    </li>
                </ul>
            </div>
        </div>
        <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="img" class="bread-shape-start position-absolute">
        <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="img"
            class="bread-shape-end position-absolute d-sm-block d-none">
    </section>
    <section class="shop-section position-relative z-1 fix section-padding pb-0">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="shop-big-details me-lg-5 wow fadeInDown" data-wow-delay="0.3s">
                        <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff"
                            class="swiper mySwiper2 mb-3">
                            <div class="swiper-wrapper">
                              
                                <div class="swiper-slide">
                                    <div class="thumbSlide-big">
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}">
                                    </div>
                                </div>
                                  @foreach (explode(',', $product->images) as $gimg)
                                <div class="swiper-slide">
                                    <div class="thumbSlide-big">
                                        <img src="{{ asset('uploads/products/' . $gimg) }}" alt="{{ $product->name }}">
                                    </div>
                                </div>
                                     @endforeach
                              
                            </div>
                            <div class="swiper-button-next"></div>
                            <div class="swiper-button-prev"></div>
                        </div>
                        <div class="swiper mySwiper">
                            <div class="swiper-wrapper">
                                  <div class="swiper-slide">
                                    <div class="thumbSlide">
                                        <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}">
                                    </div>
                                </div>
                                @foreach (explode(',', $product->images) as $gimg)
                                
                                <div class="swiper-slide">
                                    <div class="thumbSlide">
                                        <img src="{{ asset('uploads/products/' . $gimg) }}" alt="{{ $product->name }}">
                                    </div>
                                </div>
                                      @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="shop-details-right">
                        <h4 class="text-black mb-1 wow fadeInUp" data-wow-delay="0.1s">{{ $product->name }}</h4>
                        <div class="d-flex align-items-center gap-xl-3 gap-2 flex-wrap mb-3 wow fadeInUp"
                            data-wow-delay="0.2s">
                            <div class="d-flex gap-1">
                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                <i class="fa-solid fa-star fs-16 ratting-clr"></i>
                                <i class="fa-solid fa-star fs-16 text3-clr"></i>
                            </div>
                            <span class="fs-14 wow fadeInUp" data-wow-delay="0.3s">(4.8 Reviews)</span>
                        </div>
                        <h5 class="theme3-clr mb-2 wow fadeInUp" data-wow-delay="0.1s">
                            <small class="text-muted" style="font-size: 14px;">Price varies by size - select below</small>
                        </h5>
                        <div class="d-flex align-items-center gap-1 mb-xl-3 mb-2 fs-18 wow fadeInUp"
                            data-wow-delay="0.5s">
                            Availability: <span class="theme3-clr">{{ $product->stock_status == 'instock' ? 'In Stock' : 'Out of Stock' }}</span>
                        </div>
                        <p class="text-clr mb-30 fs-16 wow fadeInUp" data-wow-delay="0.6s">
                          {{ $product->short_description }}
                        </p>

                            <h5 class="fw-500 mb-1">Size</h5>
                                <div class="d-flex align-items-center gap-2 mb-lg-4 mb-3 flex-wrap">
                                    @if($product->sizes && $product->sizes->count() > 0)
                                        @foreach($product->sizes as $size)
                                            <div class="size_available w-auto px-3 py-2 rounded-3 d-center cursor-pointer size-option" 
                                                data-size-id="{{ $size->id }}" 
                                                data-size-label="{{ $size->size_label }}"
                                                data-quantity="{{ $size->quantity }}"
                                                data-regular-price="{{ $size->regular_price }}"
                                                data-sale-price="{{ $size->sale_price }}"
                                                style="border: 2px solid #ddd; transition: all 0.3s; min-width: 70px;">
                                                <div class="text-center">
                                                    <div class="fw-500">{{ $size->size_value }} {{ $size->unit }}</div>
                                                    @if($size->quantity > 0)
                                                        <small class="text-success" style="font-size: 11px;">{{ $size->quantity }} left</small>
                                                    @else
                                                        <small class="text-danger" style="font-size: 11px;">Out of Stock</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No sizes available</span>
                                    @endif
                                </div>
                                <input type="hidden" id="selected-size-id" name="selected_size_id" value="">
                                <div id="price-display" class="mb-3">
                                    <h5 id="current-price" class="theme3-clr mb-0"></h5>
                                </div>
                                <div id="size-stock-info" class="mb-3" style="display: none;">
                                    <p class="text-danger fw-500"><i class="fas fa-exclamation-triangle"></i> <span id="stock-message">This size is out of stock</span></p>
                                </div>
                        <!-- Quantity Wrapper -->
                        <div class="mb-30 wow fadeInUp" data-wow-delay="0.7s">
                            <h6 class="text-black mb-2">Quantity</h6>
                            <div class="quantity-wrapper d-inline-flex align-items-center">
                                <button type="button" class="quantityDecrement">-</button>
                                <input type="text" name="quantity_check" value="1" min="1" readonly>
                                <button type="button" class="quantityIncrement">+</button>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-xl-3 gap-2 flex-wrap mb-40 wow fadeInUp" data-wow-delay="0.8s">
                            @if (Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                            <a href="{{ route('cart.index') }}" type="button" class="theme-btn h-44px">
                                <img src="{{ asset('assets/img/icons/cart.png') }}" alt="img"> Go To Cart
                            </a>
                            @else
                            <form name="addtocart-form" method="post" action="{{ route('cart.add') }}">
                            @csrf
                               <input type="number" name="quantity" value="1" min="1"
                                        class="d-none">
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price" id="current-price-value"
                                >
                            <input type="hidden" id="form-size-id" name="size_id" value="">
                            <div class="product-single__addtocart">
                                <button type="submit" class="theme-btn h-44px" id="add-to-cart-btn">
                                <img src="{{ asset('assets/img/icons/cart.png') }}" alt="img"> Add To Cart
                            </button>
                            </div>
                            </form>
                            
                               @endif
                                @if (Cart::instance('wishlist')->content()->where('id', $product->id)->count() > 0)
                            
                             <form
                                action="{{ route('wishlist.item.remove', ['rowId' => Cart::instance('wishlist')->content()->where('id', $product->id)->first()->rowId]) }}"
                                method="POST" id="frm-remove-item">
                                @csrf
                                @method('DELETE')
                              <a href="javascript:void(0)" onclick="document.getElementById('frm-remove-item').submit();"
                                class="theme-btn h-44px fw-semibold  text-capitalize fs-14 ">
                                <img src="{{ asset('assets/img/icon/wishlist.png') }}" alt="img"> Remove From Wishlist
                           
                            </a>
                            </form>
                             @else
                            <form action="{{ route('wishlist.add') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $product->id }}">
                                <input type="hidden" name="name" value="{{ $product->name }}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="price"
                                    value="{{ $product->sale_price == '' ? $product->regular_price : $product->sale_price }}">
                                <button type="submit"
                                    class="theme-btn h-44px fw-semibold  text-capitalize fs-14 ">
                                    <span><img src="{{ asset('assets/img/icon/wishlist.png') }}" alt="img"> Add To Wishlist</span>
                                </button>
                            </form>
                            @endif
                        </div>
                        <div class="d-flex align-items-center gap-3 flex-wrap wow fadeInUp" data-wow-delay="0.9s">
                            <h6 class="text-black mb-2">Social Share:</h6>
                            <div class="soical-gray d-flex align-items-center gap-2">
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(Request::url()) }}" target="_blank" class="social" title="Share on Facebook">
                                    <i class="fa-brands fa-facebook-f fs-18"></i>
                                </a>
                               
                             

                                <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' - ' . Request::url()) }}" target="_blank" class="social" title="Share on WhatsApp">
                                    <i class="fa-brands fa-whatsapp fs-18"></i>
                                </a>
                              
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <img src="{{ asset('assets/img/inner-global-pasta.png') }}" alt="img"
            class="position-absolute top-0 end-0 float-bob-y pt-100 mt-4 z-n1 d-sm-block d-none">
    </section>

    <!--- Shop Description Section -->
    <section class="shop-description-section fix section-padding pt-0">
        <div class="container pt-100">
            <div class="shop-description_inner">
                <ul class="nav d-flex flex-wrap align-items-center nav-tabs border-0 mb-4" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link p-0 active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                            type="button" role="tab" aria-controls="home" aria-selected="true">
                            Product Description
                        </button>
                    </li>
                    {{-- <li class="nav-item" role="presentation">
                        <button class="nav-link p-0" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                            type="button" role="tab" aria-controls="profile" aria-selected="false">
                            Specification
                        </button>
                    </li> --}}
                    {{-- <li class="nav-item" role="presentation">
                        <button class="nav-link p-0" id="profile-tab01" data-bs-toggle="tab" data-bs-target="#profile01"
                            type="button" role="tab" aria-controls="profile01" aria-selected="false">
                            (4) Reviews
                        </button>
                    </li> --}}
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <p class="fs-16 heading-font mb-sm-3 mb-2">
                            {{ $product->description }}
                        </p>
                      
                    </div>
                    {{-- <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        <p class="fs-16 heading-font mb-sm-3 mb-2">
                            Indulge in the bold flavors of our Spicy Mushroom Pasta — a perfect fusion of heat and
                            creaminess. Fresh mushrooms are
                            sautéed with garlic,
                            chili flakes, and herbs, then blended into a rich, spicy sauce that coats every strand of
                            pasta. Ideal for those who
                            crave a kick in every bite,
                            this dish delivers warmth, comfort, and irresistible taste in one bowl.
                        </p>
                    </div> --}}
                    {{-- <div class="tab-pane fade" id="profile01" role="tabpanel" aria-labelledby="profile-tab01">
                        <p class="fs-16 heading-font mb-sm-3 mb-2">
                            Indulge in the bold flavors of our Spicy Mushroom Pasta — a perfect fusion of heat and
                            creaminess. Fresh mushrooms are
                            sautéed with garlic,
                            chili flakes, and herbs, then blended into a rich, spicy sauce that coats every strand of
                            pasta. Ideal for those who
                            crave a kick in every bite,
                            this dish delivers warmth, comfort, and irresistible taste in one bowl.
                        </p>
                    </div> --}}
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // Size selection handling
        let selectedSizeId = null;
        let selectedSizeQuantity = 0;
        let selectedRegularPrice = 0;
        let selectedSalePrice = 0;

        // Function to update price display
        function updatePriceDisplay() {
            let priceHtml = '';
            const regularPrice = parseFloat(selectedRegularPrice) || 0;
            const salePrice = parseFloat(selectedSalePrice) || 0;
            
            let finalPrice = regularPrice;
            if(salePrice && salePrice > 0) {
                priceHtml = '<s>Rs ' + regularPrice.toFixed(2) + '</s> <span class="text-success fw-bold">Rs ' + salePrice.toFixed(2) + '</span>';
                finalPrice = salePrice;
            } else if(regularPrice > 0) {
                priceHtml = '<span class="fw-bold fs-5">Rs ' + regularPrice.toFixed(2) + '</span>';
                finalPrice = regularPrice;
            } else {
                priceHtml = '<span class="text-muted">Price not set</span>';
                finalPrice = 0;
            }
            $('#current-price').html(priceHtml);
            $('#current-price-value').val(finalPrice);
        }

        $('.size-option').on('click', function() {
            // Remove previous selection
            $('.size-option').css({
                'border': '2px solid #ddd',
                'background-color': 'transparent'
            });
            
            // Highlight selected size
            $(this).css({
                'border': '2px solid #4CAF50',
                'background-color': '#f0f8f5'
            });
            
            // Get size details
            selectedSizeId = $(this).data('size-id');
            selectedSizeQuantity = $(this).data('quantity');
            selectedRegularPrice = $(this).data('regular-price');
            selectedSalePrice = $(this).data('sale-price');
            const sizeLabel = $(this).data('size-label');
            
            // Update price display
            updatePriceDisplay();
            
            // Update hidden input
            $('#selected-size-id').val(selectedSizeId);
            $('#form-size-id').val(selectedSizeId);
            
            // Show/hide stock info based on quantity
            if(selectedSizeQuantity <= 0) {
                $('#size-stock-info').show();
                $('#stock-message').text('This size is out of stock');
                $('#add-to-cart-btn').prop('disabled', true).css('opacity', '0.5');
            } else {
                $('#size-stock-info').hide();
                $('#add-to-cart-btn').prop('disabled', false).css('opacity', '1');
            }
        });
        
        // Auto-select first size if available
        const firstSize = $('.size-option').first();
        if(firstSize.length) {
            firstSize.click();
        }
        
        // Validate size selection on form submit
        $('form[name="addtocart-form"]').on('submit', function(e) {
            const hasSizes = $('.size-option').length > 0;
            if(hasSizes && !selectedSizeId) {
                e.preventDefault();
                alert('Please select a size before adding to cart');
                return false;
            }
            
            if(selectedSizeQuantity <= 0) {
                e.preventDefault();
                alert('This size is out of stock');
                return false;
            }
        });
        
        // Quantity increment/decrement handling
        $('.quantityIncrement, .quantityDecrement').on('click', function() {
            var input = $('input[name="quantity_check"]');
            var currentVal = parseInt(input.val());
            
            // Allow time for any existing increment/decrement logic to update the input
            setTimeout(function() {
                var newVal = parseInt(input.val());
                $('input[name="quantity"]').val(newVal);
                
                // Check if quantity exceeds available stock
                if(selectedSizeQuantity > 0 && newVal > selectedSizeQuantity) {
                    alert('Only ' + selectedSizeQuantity + ' items available for this size');
                    input.val(selectedSizeQuantity);
                    $('input[name="quantity"]').val(selectedSizeQuantity);
                }
            }, 50);
        });
    });
</script>
@endpush
