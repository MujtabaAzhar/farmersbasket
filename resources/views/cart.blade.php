@extends('layouts.app')
@section('content')


<!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Cart
                </h2>
                <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                    <li>
                        <a href="{{ route('home.index') }}" >
                            Home
                        </a>
                    </li>
                   
                    <li>
                        <i class="fa-solid fa-angle-right"></i>
                    </li>
                    <li>
                        Cart
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
                @if($items->count() > 0)
            <div class="row g-4">
                <div class="col-lg-9">
                    <div class="table-cart-inner p-xxl-4 p-xl-4 p-3">
                        <div class="table-responsive">
                                 @if(Session::has('success'))
                <p class="text-success text-center mb-3">{{ Session::get('success') }}</p>
            @endif
            @if(Session::has('error'))
                <p class="text-danger text-center mb-3">{{ Session::get('error') }}</p>
            @endif
                            <table class="table m-0 align-middle table-borderless">
                                <thead>
                                    <tr>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Product</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Price</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Quantity</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Subtotal</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Remove</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                       @foreach ($items as $item)
                                    <tr class="border overflow-hidden rounded">
                                        <td class="p-3">
                                            <a href="checkout.html" class="d-flex align-items-center gap-3">
                                                <img src="{{asset('uploads/products/thumbnails/' . $item->model->image)}}" width="120" height="120" alt="{{ $item->name}}"
                                                    class="border rounded-2">
                                                <h5 class="text-black max-w-180 fw-500">{{ $item->name }}</h5>
                                            </a>
                                        </td>
                                        <td class="p-3">
                                            <h5 class="theme-clr fw-500">Rs {{ $item->price }}</h5>
                                        </td>
                                        <td class="p-3">
                         
                                            <div class="wow fadeInUp" >
                                                <div class="quantity-wrapper d-inline-flex align-items-center">
                                                    <form action="{{ route('cart.qty.decrease',['rowId' => $item->rowId]) }}" method="POST">
                        @csrf
                        @method('PUT')
                                                    <button type="button" class="quantityDecrement qty-control__reduce">-</button>
                                                          </form>
                                                    <input type="text" name="quantity" value="{{ $item->qty }}" min="1" readonly class="qty-control__number">
                                                      <form action="{{ route('cart.qty.increase',['rowId' => $item->rowId]) }}" method="POST">
                        @csrf
                        @method('PUT')
                                                    <button type="button" class="quantityIncrement qty-control__increase">+</button>
                                                            </form>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <h5 class="p-3 theme-clr fw-500">Rs {{ $item->subTotal() }}</h5>
                                        </td>

                                        <td class="text-center">
                                           <form action="{{ route('cart.item.remove', ['rowId' => $item->rowId]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                     <a href="javascript:void(0);" class="remove-cart">
                 <i
                                                    class="fa-solid fa-xmark"></i>
                  </a>
                  </form>
                                           
                                        </td>
                                    </tr>
                                      @endforeach
                                
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center gap-4 mt-4">
                             <form action="{{ route('cart.empty') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="theme-btn">CLEAR CART</button>
            </form>
             
                           
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="d-flex flex-column gap-3">
                        <div class="shadow-cus coupon-group p-xl-4 p-3 rounded-3 bg-white wow fadeInDown"
                            data-wow-delay="4.s">
                       
                            <h5 class="border-bottom pb-2 mb-3">Coupon Code</h5>
                             @if(!Session::has('coupon'))
                              <form action="{{ route('cart.apply_coupon') }}" method="POST" class="position-relative bg-body">
                @csrf
              <input class=" mb-3" type="text" name="coupon_code" placeholder="Coupon Code" value="@if(Session::has('coupon')){{Session::get('coupon')['code']}} Applied!@endif">
              <input class="theme-btn justify-content-center w-100 btn-outline-theme" type="submit"
                value="APPLY COUPON">
            </form>
             @else
              <form action="{{ route('cart.remove_coupon') }}" method="POST" class="position-relative bg-body">
                @csrf
                @method('DELETE')
              <input class=" mb-3" type="text" name="coupon_code" placeholder="Coupon Code" value="@if(Session::has('coupon')){{Session::get('coupon')['code']}} Applied!@endif">
              <input class="theme-btn justify-content-center w-100 btn-outline-theme" type="submit"
                value="REMOVE COUPON">
            </form>
            @endif
                           
                           
                        </div>
                        <div class="shadow-cus coupon-group p-xl-4 p-3 rounded-3 bg-white wow fadeInDown"
                            data-wow-delay="6.s">
                            <h5 class="border-bottom pb-2 mb-3">Order Summary</h5>
                            <div class="d-flex flex-column gap-2">
                                @if(Session::has('discounts'))
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Subtotal</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Cart::instance('cart')->subtotal() }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Discount {{ Session::get('coupon')['code'] }}</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Session::get('discounts')['discount'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Subtotal</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Session::get('discounts')['subtotal'] }}</span>
                                </div>
                                 <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Shipping</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Session::get('discounts')['shipping'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">VAT</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Session::get('discounts')['tax'] }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Total</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Session::get('discounts')['total'] }}</span>
                                </div>
                                @else
                                  <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Subtotal</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Cart::instance('cart')->subtotal() }}</span>
                                </div>
                             
                                 <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Shipping</span>
                                    
                                    <span class="fs-16 text-black fw-medium">Rs  {{$shipping_fee}}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">VAT</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Cart::instance('cart')->tax() }}</span>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <span class="fs-16 text-color">Total</span>
                                    <span class="fs-16 text-black fw-medium">Rs {{ Cart::instance('cart')->total() }}</span>
                                </div>
                                @endif
                                  <a href="{{ route('cart.checkout') }}" class="theme-btn text-center justify-content-center w-100 mt-3">PROCEED TO CHECKOUT</a>
                             
                            </div>
                        </div>
                     
                    </div>
                </div>
            </div>
            @else
        <div class="row">
            <div class="col-md-12 text-center pt-5 bp-5">
                <p class="mb-4">Your cart is currently empty.</p>
                <a href="{{ route('shop.index') }}" class="theme-btn">Shop Now</a>
            </div>
        </div>
        @endif
        </div>
        <img src="{{ asset('assets/img/inner-global-pasta.png') }}" alt="img"
            class="position-absolute bottom-0 pb-100 end-0 float-bob-y mt-4 z-n1 d-sm-block d-none">
    </section>

    

   


   
@endsection

@push('scripts')

<script>
  $(function() {
    $('.qty-control__reduce').on('click', function() {
      $(this).closest('.qty-control').find('.qty-control__number').trigger('change');
      $(this).closest('form').submit();
    });
    $('.qty-control__increase').on('click', function() {
      $(this).closest('.qty-control').find('.qty-control__number').trigger('change');
      $(this).closest('form').submit();
    });
    $('.remove-cart').on('click', function() {
      $(this).closest('form').submit();
    });
  });
</script>
@endpush

