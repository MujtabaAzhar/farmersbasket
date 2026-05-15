@extends('layouts.app')
@section('content')
   <!-- Hero section start -->
    <section class="breadcrumb-section position-relative fix bg-cover"
        style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
        <div class="container">
            <div class="breadcrumb-content">
                <h2 class="white-clr fw-semibold text-center heading-font mb-2">
                    Wishlist
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
                        Wishlist
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
           @if(Cart::instance('wishlist')->count() > 0)
            <div class="row g-4">
                <div class="col-lg-12">
                    <div class="table-cart-inner p-xxl-4 p-xl-4 p-3">
                        <div class="table-responsive">
                            <table class="table m-0 align-middle table-borderless">
                                <thead>
                                    <tr>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Products</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Price</div>
                                        </th>
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Quantity</div>
                                        </th>
                                        
                                        <th class="pb-lg-4 pb-3">
                                            <div class="fs-18 fw-semibold text-black m-0">Action</div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($items as $item)
                                    <tr class="border overflow-hidden rounded">
                                        <td class="p-3">
                                            <a href="checkout.html" class="d-flex align-items-center gap-3">
                                                <img src="{{ asset('uploads/products/thumbnails/' . $item->model->image) }}" alt=""
                                                    class="border rounded-2">
                                                <h5 class="text-black max-w-180 fw-500">{{ $item->name }}</h5>
                                            </a>
                                        </td>
                                        <td class="p-3">
                                            <h5 class="theme-clr fw-500">Rs {{ $item->price }}</h5>
                                        </td>
                                        <td class="p-3">
                                           <h5 class="text-black max-w-180 fw-500">    {{ $item->qty }}</h5>
                                       
                                        </td>
                                       
                                        <td class="text-center">
                                            <div class="row">
                        <div class="col-6">
  <form method="POST" action="{{ route('wishlist.move_to_cart', ['rowId' => $item->rowId]) }}">
                        @csrf
                        <button type="submit" class="btn btn-sm theme-btn">Move to Cart</button> 
                    </form>
                        </div>
                        <div class="col-6">
 <form action="{{ route('wishlist.item.remove', ['rowId' => $item->rowId]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <a href="javascript:void(0)" class="remove-cart" onclick="this.closest('form').submit()">
                          <i
                                                    class="fa-solid fa-xmark"></i>
                        </a>
                    </form>
                        </div>
                    </div>
                                    
                                        </td>
                                    </tr>
                                      @endforeach
                               
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex align-items-center gap-4 mt-4">
                           <form action="{{ route('wishlist.empty') }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="theme-btn rounded-2">CLEAR WISHLIST</button>
            </form>
                          
                        </div>
                    </div>
                </div>
               
            </div>
              @else
              <div class="row">
            <div class="col-md-12 text-center pt-5 bp-5">
                <p class="mb-3">No items in your wishlist.</p>
                <a href="{{route('shop.index')}}" class="theme-btn">Wishlist Now</a>
            </div>
        </div>
             @endif
        </div>
        <img src="assets/img/inner-global-pasta.png" alt="img"
            class="position-absolute bottom-0 pb-100 end-0 float-bob-y mt-4 z-n1 d-sm-block d-none">
    </section>

     

   

   


 

@endsection
