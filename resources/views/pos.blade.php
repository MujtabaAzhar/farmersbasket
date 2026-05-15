@extends('layouts.app')
@section('content')
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    POS
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
                        POS
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
   
  <!-- Hero section start -->
  
    <!-- Popular Section start -->
    <section class="populars-section position-relative section-padding fix wow fadeInDown" data-wow-delay=".4s">
        <div class="container">
            <div class="row g-3">
                  @foreach ($products as $product)
                <div class="col-lg-6 col-md-6 col-xxl-4">
                    <div class="most-popular-card bg-white card-effect smooth d-flex align-items-xxl-center justify-content-between gap-2 border rounded-12 p-xl-4 p-3 wow fadeInUp"
                        data-wow-delay="0.3s">
                        <div class="cont">
                            <h6 class="mb-lg-1 mb-1"><a href="{{ route('shop.product.details', ['product_slug' => $product->slug]) }}"{{ $product->name }}" class="link-effect">{{ $product->name }}</a></h6>
                            <p class="fs-15 mb-lg-2 mb-1 max-w-200 lh-base">  {{ $product->short_description }}</p>
                            <h6 class="theme3-clr fs-16 fw-bold"> @if ($product->sale_price)
                                                            <del
                                                                class="fs-16 text4-clr">Rs {{ $product->regular_price }}</del>
                                                            <span
                                                                class="theme3-clr fw-semibold fs-16">Rs {{ $product->sale_price }}</span>
                                                        @else
                                                            <span class="theme3-clr fw-semibold fs-16">Rs
                                                                {{ $product->regular_price }}</span>
                                                        @endif</h6>
                        </div>
                        <div class="thumb rounded-2 position-relative w-90px h-90px">
                            <img width="90" height="90" src="{{ asset('uploads/products/' . $product->image) }}" alt="img"
                                class="rounded-2">
                            <a href="#0" data-bs-toggle="modal" data-bs-target="#exampleModal" data-product-id="{{ $product->id }}"
                                class="w-28px h-28px z-1 position-absolute bottom-0 end-0 m-2 bg-white rounded d-center theme3-clr fs-14 product-quick-view-btn">
                                <i class="fa-solid fa-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
               @endforeach
            </div>
            <ul class="pagination d-flex align-items-center gap-1 justify-content-center flex-wrap mt-40">
                 {{ $products->links('pagination::bootstrap-5') }}
               
            </ul>
        </div>
        <img src="assets/img/inner-global-left.png" alt="img"
            class="position-absolute bottom-0 pb-100 mb-5 start-0 float-bob-y pt-5 z-n1 d-sm-block d-none">
        <img src="assets/img/inner-global-pasta.png" alt="img"
            class="position-absolute top-40 end-0 float-bob-y z-n1 d-sm-block d-none">
    </section>


    <form id="frmfilter" method="GET" action="{{ route('shop.index') }}">
        <input type="hidden" name="page" value="{{ $products->currentPage() }}" />
        <input type="hidden" name="size" id="size" value="{{ $size }}" />
        <input type="hidden" name="order" id="order" value="{{ $order }}" />
        <input type="hidden" name="brands" id="hdnBrands" />
        <input type="hidden" name="categories" id="hdnCategories" />
        <input type="hidden" name="min" id="hdnMinPrice" value="{{ $min_price }}" />
        <input type="hidden" name="max" id="hdnMaxPrice" value="{{ $max_price }}" />
    </form>

      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        Quick View
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6">
                            <div class="thumb w-100 rounded-4">
                                <img id="modal-product-image" src="" alt="img" class="w-100 rounded-4" style="max-height: 300px; object-fit: cover;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="quick-view-content">
                                <h3 id="modal-product-name" class="mb-2 fw-semibold">Loading...</h3>
                                <p id="modal-product-description" class="fs-16 text-clr mb-lg-4 mb-3">
                                    Loading...
                                </p>
                                <h5 class="fw-500 mb-1">Size</h5>
                               
                                <div id="modal-sizes-container" class="d-flex gap-lg-2 gap-1 mb-lg-4 mb-3 flex-wrap">
                                    <p class="text-muted">Loading sizes...</p>
                                </div>
                                <input type="hidden" id="modal-selected-size-id" name="selected_size_id" value="">
                                <div id="modal-price-display" class="mb-3">
                                    <h5 id="modal-current-price" class="theme3-clr mb-0"></h5>
                                </div>
                                <div id="modal-size-stock-info" class="mb-3" style="display: none;">
                                    <p class="text-danger fw-500"><i class="fas fa-exclamation-triangle"></i> <span id="modal-stock-message">This size is out of stock</span></p>
                                </div>
                                <div class="mb-3 d-flex flex-wrap align-items-center gap-xxl-2 gap-1">
                                    <div class="quantity-wrapper d-inline-flex align-items-center">
                                        <button type="button" class="modal-quantityDecrement">-</button>
                                        <input type="text" id="modal-quantity-input" value="1" readonly>
                                        <button type="button" class="modal-quantityIncrement">+</button>
                                    </div>
                                    <form id="modal-add-to-cart-form" method="POST" action="{{ route('cart.add') }}" style="display: inline;">
                                        @csrf
                                        <input type="hidden" id="modal-product-id" name="id" value="">
                                        <input type="hidden" id="modal-product-name-input" name="name" value="">
                                        <input type="hidden" id="modal-product-price" name="price" value="">
                                        <input type="hidden" id="modal-form-size-id" name="size_id" value="">
                                        <input type="hidden" id="modal-product-quantity" name="quantity" value="1">
                                        <button type="submit" class="theme-btn fs-14 px-3 text-nowrap" id="modal-add-to-cart-btn">
                                            Add to Cart
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        let modalSelectedSizeId = null;
        let modalSelectedSizeQuantity = 0;
        let modalSelectedRegularPrice = 0;
        let modalSelectedSalePrice = 0;

        $(function() {
            // Filter and pagination handlers
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

            // Modal Quick View Handlers
            $('.product-quick-view-btn').on('click', function(e) {
                e.preventDefault();
                const productId = $(this).data('product-id');
                loadProductData(productId);
            });

            // When modal is hidden, reset values
            $('#exampleModal').on('hidden.bs.modal', function() {
                resetModalData();
            });

            // Modal Quantity increment/decrement
            $(document).on('click', '.modal-quantityIncrement', function() {
                const input = $('#modal-quantity-input');
                input.val(parseInt(input.val(), 10) + 1);
                $('#modal-product-quantity').val(input.val());
                
                // Check if quantity exceeds available stock
                if(modalSelectedSizeQuantity > 0 && parseInt(input.val()) > modalSelectedSizeQuantity) {
                    alert('Only ' + modalSelectedSizeQuantity + ' items available for this size');
                    input.val(modalSelectedSizeQuantity);
                    $('#modal-product-quantity').val(modalSelectedSizeQuantity);
                }
            });

            $(document).on('click', '.modal-quantityDecrement', function() {
                const input = $('#modal-quantity-input');
                const currentVal = parseInt(input.val(), 10);
                if (currentVal > 1) {
                    input.val(currentVal - 1);
                    $('#modal-product-quantity').val(currentVal - 1);
                }
            });

            // Modal Add to Cart Form Submit
            $('#modal-add-to-cart-form').on('submit', function(e) {
                if(!modalSelectedSizeId) {
                    e.preventDefault();
                    alert('Please select a size before adding to cart');
                    return false;
                }
                
                if(modalSelectedSizeQuantity <= 0) {
                    e.preventDefault();
                    alert('This size is out of stock');
                    return false;
                }
            });
        });

        function loadProductData(productId) {
            $.ajax({
                url: '{{ url("/api/product") }}/' + productId,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    populateModal(data);
                },
                error: function(error) {
                    console.error('Error loading product data:', error);
                    alert('Error loading product data');
                }
            });
        }

        function populateModal(product) {
            // Set basic product information
            $('#modal-product-name').text(product.name);
            $('#modal-product-description').text(product.short_description);
            $('#modal-product-image').attr('src', '{{ asset("uploads/products") }}/' + product.image);
            
            // Set hidden form inputs
            $('#modal-product-id').val(product.id);
            $('#modal-product-name-input').val(product.name);
            
            // Reset quantity
            $('#modal-quantity-input').val(1);
            $('#modal-product-quantity').val(1);
            
            // Clear previous sizes
            $('#modal-sizes-container').empty();
            
            // Populate sizes
            if(product.sizes && product.sizes.length > 0) {
                product.sizes.forEach(function(size) {
                    const sizeHtml = `
                        <div class="modal-size-option w-auto px-3 py-2 rounded-3 d-center cursor-pointer" 
                            data-size-id="${size.id}" 
                            data-size-label="${size.size_label}"
                            data-quantity="${size.quantity}"
                            data-regular-price="${size.regular_price}"
                            data-sale-price="${size.sale_price}"
                            style="border: 2px solid #ddd; transition: all 0.3s; min-width: 70px; cursor: pointer;">
                            <div class="text-center">
                                <div class="fw-500">${size.size_value} ${size.unit}</div>
                                ${size.quantity > 0 ? 
                                    `<small class="text-success" style="font-size: 11px;">${size.quantity} left</small>` : 
                                    `<small class="text-danger" style="font-size: 11px;">Out of Stock</small>`
                                }
                            </div>
                        </div>
                    `;
                    $('#modal-sizes-container').append(sizeHtml);
                });
                
                // Add click handler for size selection
                $('.modal-size-option').on('click', function() {
                    selectModalSize($(this));
                });
                
                // Auto-select first size
                $('.modal-size-option').first().click();
            } else {
                $('#modal-sizes-container').html('<span class="text-muted">No sizes available</span>');
            }
        }

        function selectModalSize(element) {
            // Remove previous selection
            $('.modal-size-option').css({
                'border': '2px solid #ddd',
                'background-color': 'transparent'
            });
            
            // Highlight selected size
            element.css({
                'border': '2px solid #4CAF50',
                'background-color': '#f0f8f5'
            });
            
            // Get size details
            modalSelectedSizeId = element.data('size-id');
            modalSelectedSizeQuantity = element.data('quantity');
            modalSelectedRegularPrice = element.data('regular-price');
            modalSelectedSalePrice = element.data('sale-price');
            
            // Update form inputs
            $('#modal-selected-size-id').val(modalSelectedSizeId);
            $('#modal-form-size-id').val(modalSelectedSizeId);
            
            // Update price display
            updateModalPriceDisplay();
            
            // Show/hide stock info
            if(modalSelectedSizeQuantity <= 0) {
                $('#modal-size-stock-info').show();
                $('#modal-stock-message').text('This size is out of stock');
                $('#modal-add-to-cart-btn').prop('disabled', true).css('opacity', '0.5');
            } else {
                $('#modal-size-stock-info').hide();
                $('#modal-add-to-cart-btn').prop('disabled', false).css('opacity', '1');
            }
        }

        function updateModalPriceDisplay() {
            let priceHtml = '';
            const regularPrice = parseFloat(modalSelectedRegularPrice) || 0;
            const salePrice = parseFloat(modalSelectedSalePrice) || 0;
            
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
            
            $('#modal-current-price').html(priceHtml);
            $('#modal-product-price').val(finalPrice);
        }

        function resetModalData() {
            modalSelectedSizeId = null;
            modalSelectedSizeQuantity = 0;
            modalSelectedRegularPrice = 0;
            modalSelectedSalePrice = 0;
            $('#modal-sizes-container').empty();
            $('#modal-quantity-input').val(1);
        }
    </script>
@endpush
