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
                            @php $avgRating = $product->averageRating(); $reviewCount = $product->approvedReviews()->count(); @endphp
                            <div class="d-flex gap-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star fs-16 {{ $i <= round($avgRating) ? 'ratting-clr' : 'text3-clr' }}"></i>
                                @endfor
                            </div>
                            <span class="fs-14 wow fadeInUp" data-wow-delay="0.3s">
                                ({{ $avgRating > 0 ? $avgRating : 'No' }} {{ $reviewCount === 1 ? 'Review' : 'Reviews' }})
                            </span>
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

                            <h5 class="fw-500 mb-1">Size / Variant</h5>
                                <div class="d-flex align-items-center gap-2 mb-lg-4 mb-3 flex-wrap">
                                    @if($product->variants && $product->variants->count() > 0)
                                        @foreach($product->variants->where('is_active', true) as $variant)
                                            <div class="size_available w-auto px-3 py-2 rounded-3 d-center cursor-pointer size-option"
                                                data-variant-id="{{ $variant->id }}"
                                                data-variant-label="{{ $variant->display_label }}"
                                                data-stock-qty="{{ $variant->stock_qty }}"
                                                data-price="{{ $variant->price }}"
                                                data-compare-price="{{ $variant->compare_price }}"
                                                style="border: 2px solid #ddd; transition: all 0.3s; min-width: 70px; {{ !$variant->isInStock() ? 'opacity:0.5;' : '' }}">
                                                <div class="text-center">
                                                    <div class="fw-500">{{ $variant->variant_name }}</div>
                                                    @if($variant->weight)
                                                        <div class="text-muted" style="font-size:11px;">{{ $variant->weight }} {{ $variant->unit }}</div>
                                                    @endif
                                                    @if($variant->isInStock())
                                                        <small class="text-success" style="font-size:11px;">{{ $variant->stock_qty }} left</small>
                                                    @else
                                                        <small class="text-danger" style="font-size:11px;">Out of Stock</small>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No variants available</span>
                                    @endif
                                </div>
                                <input type="hidden" id="selected-variant-id" value="">
                                <div id="price-display" class="mb-3">
                                    <h5 id="current-price" class="theme3-clr mb-0"></h5>
                                </div>
                                <div id="size-stock-info" class="mb-3" style="display: none;">
                                    <p class="text-danger fw-500"><i class="fas fa-exclamation-triangle"></i> <span id="stock-message">This variant is out of stock</span></p>
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
                            <input type="number" name="quantity" value="1" min="1" class="d-none">
                            <input type="hidden" name="id" value="{{ $product->id }}">
                            <input type="hidden" name="name" value="{{ $product->name }}">
                            <input type="hidden" name="price" id="current-price-value">
                            <input type="hidden" id="form-variant-id" name="variant_id" value="">
                            <div class="product-single__addtocart">
                                <button type="submit" class="theme-btn h-44px" id="add-to-cart-btn">
                                <img src="{{ asset('assets/img/icons/cart.png') }}" alt="img"> Add To Cart
                            </button>
                            </div>
                            </form>
                            
                               @endif
                             @if ($wishlisted)
                                <form action="{{ route('wishlist.remove.product', $product->id) }}" method="POST" id="frm-remove-item">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="theme-btn h-44px fw-semibold text-capitalize fs-14">
                                        <img src="{{ asset('assets/img/icon/wishlist.png') }}" alt="img"> Remove From Wishlist
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('wishlist.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $product->id }}">
                                    <input type="hidden" name="name" value="{{ $product->name }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="price" id="wishlist-price" value="{{ $product->min_price ?? 0 }}">
                                    <button type="submit" class="theme-btn h-44px fw-semibold text-capitalize fs-14">
                                        <img src="{{ asset('assets/img/icon/wishlist.png') }}" alt="img"> Add To Wishlist
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
                    <li class="nav-item" role="presentation">
                        <button class="nav-link p-0" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews"
                            type="button" role="tab" aria-controls="reviews" aria-selected="false">
                            ({{ $product->approvedReviews()->count() }}) Reviews
                        </button>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <p class="fs-16 heading-font mb-sm-3 mb-2">
                            {{ $product->description }}
                        </p>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">

                        {{-- Session flash --}}
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        {{-- Approved reviews list --}}
                        @forelse($product->approvedReviews as $review)
                            <div class="border rounded p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>{{ $review->user->name }}</strong>
                                        <div class="d-flex gap-1 my-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fa-solid fa-star fs-12 {{ $i <= $review->rating ? 'ratting-clr' : 'text3-clr' }}"></i>
                                            @endfor
                                        </div>
                                        @if($review->title)
                                            <p class="fw-semibold mb-1">{{ $review->title }}</p>
                                        @endif
                                        <p class="fs-14 text-clr mb-0">{{ $review->comment }}</p>
                                    </div>
                                    <div class="d-flex flex-column align-items-end gap-1">
                                        <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                                        @auth
                                            @if(Auth::id() === $review->user_id)
                                                <form action="{{ route('review.destroy', $review->id) }}" method="POST">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No reviews yet. Be the first to review this product.</p>
                        @endforelse

                        {{-- Review form — only verified buyers who haven't reviewed yet --}}
                        @auth
                            @if($can_review)
                                <hr>
                                <h5 class="mb-3">Write a Review</h5>
                                <form action="{{ route('review.store', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Rating <span class="text-danger">*</span></label>
                                        <div class="d-flex gap-2">
                                            @for($i = 1; $i <= 5; $i++)
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="rating" id="star{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="star{{ $i }}">{{ $i }} ★</label>
                                                </div>
                                            @endfor
                                        </div>
                                        @error('rating') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3">
                                        <input type="text" name="title" class="form-control" placeholder="Review title (optional)" value="{{ old('title') }}">
                                    </div>
                                    <div class="mb-3">
                                        <textarea name="comment" class="form-control" rows="4" placeholder="Share your experience with this product...">{{ old('comment') }}</textarea>
                                    </div>
                                    <button type="submit" class="theme-btn">Submit Review</button>
                                </form>
                            @else
                                <p class="text-muted fs-14 mt-3">
                                    Only verified buyers can leave a review.
                                    @if(!Auth::user()->wishlistItems()->exists())
                                        Purchase this product first to share your experience.
                                    @endif
                                </p>
                            @endif
                        @else
                            <p class="text-muted fs-14 mt-3">
                                <a href="{{ route('login') }}">Log in</a> and purchase this product to leave a review.
                            </p>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
<script>
    $(document).ready(function() {
        // Variant selection handling
        let selectedVariantId = null;
        let selectedStockQty = 0;
        let selectedPrice = 0;
        let selectedComparePrice = 0;

        function updatePriceDisplay() {
            const price = parseFloat(selectedPrice) || 0;
            const compare = parseFloat(selectedComparePrice) || 0;
            let priceHtml = '';
            if (compare > 0 && compare > price) {
                priceHtml = '<s class="text-muted">Rs ' + compare.toFixed(0) + '</s> <span class="text-success fw-bold">Rs ' + price.toFixed(0) + '</span>';
            } else if (price > 0) {
                priceHtml = '<span class="fw-bold fs-5">Rs ' + price.toFixed(0) + '</span>';
            } else {
                priceHtml = '<span class="text-muted">Select a variant</span>';
            }
            $('#current-price').html(priceHtml);
            $('#current-price-value').val(price);
            $('#wishlist-price').val(price);
        }

        $('.size-option').on('click', function() {
            $('.size-option').css({'border': '2px solid #ddd', 'background-color': 'transparent'});
            $(this).css({'border': '2px solid #4CAF50', 'background-color': '#f0f8f5'});

            selectedVariantId  = $(this).data('variant-id');
            selectedStockQty   = parseInt($(this).data('stock-qty')) || 0;
            selectedPrice      = $(this).data('price');
            selectedComparePrice = $(this).data('compare-price');

            updatePriceDisplay();

            $('#selected-variant-id').val(selectedVariantId);
            $('#form-variant-id').val(selectedVariantId);

            if (selectedStockQty <= 0) {
                $('#size-stock-info').show();
                $('#stock-message').text('This variant is out of stock');
                $('#add-to-cart-btn').prop('disabled', true).css('opacity', '0.5');
            } else {
                $('#size-stock-info').hide();
                $('#add-to-cart-btn').prop('disabled', false).css('opacity', '1');
            }
        });

        // Auto-select first in-stock variant
        const firstInStock = $('.size-option').filter(function(){ return parseInt($(this).data('stock-qty')) > 0; }).first();
        if (firstInStock.length) {
            firstInStock.click();
        } else {
            $('.size-option').first().click();
        }

        // Validate variant selection on form submit
        $('form[name="addtocart-form"]').on('submit', function(e) {
            if ($('.size-option').length > 0 && !selectedVariantId) {
                e.preventDefault();
                alert('Please select a variant before adding to cart');
                return false;
            }
            if (selectedStockQty <= 0) {
                e.preventDefault();
                alert('This variant is out of stock');
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
