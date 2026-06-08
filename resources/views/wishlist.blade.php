@extends('layouts.app')
@section('content')

<!-- Hero -->
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">Wishlist</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>Wishlist</li>
            </ul>
        </div>
    </div>
    <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="img" class="bread-shape-start position-absolute">
    <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="img" class="bread-shape-end position-absolute d-sm-block d-none">
</section>

<section class="shop-section position-relative z-1 fix section-padding">
    <div class="container">

        @if($items->count() > 0)
        <div class="table-cart-inner p-xxl-4 p-xl-4 p-3">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="text-black mb-0">
                    My Wishlist
                    <span class="fs-15 text-muted fw-normal ms-2">({{ $items->count() }} item{{ $items->count() != 1 ? 's' : '' }})</span>
                </h4>
                <form action="{{ route('wishlist.empty') }}" method="POST"
                      onsubmit="return confirm('Clear your entire wishlist?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">Clear All</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table m-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:280px;">Product</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Availability</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        @auth
                        {{-- ── Logged-in: DB-backed items ────────────────── --}}
                        @foreach($items as $item)
                        @php
                            $product   = $item->product;
                            $inStock   = $product->stock_status !== 'outofstock';
                            $minPrice  = $product->variants->where('is_active', true)->min('price');
                            $maxPrice  = $product->variants->where('is_active', true)->max('price');
                            $totalStock = $product->variants->where('is_active', true)->sum('stock_qty');
                        @endphp
                        <tr>
                            {{-- Product --}}
                            <td class="p-3">
                                <a href="{{ route('shop.product.details', $product->slug) }}"
                                   class="d-flex align-items-center gap-3 text-decoration-none">
                                    <div class="position-relative flex-shrink-0">
                                        @if($product->image)
                                        <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}"
                                             alt="{{ $product->name }}"
                                             class="rounded-2 border"
                                             style="width:70px;height:70px;object-fit:cover;">
                                        @else
                                        <div class="rounded-2 border d-flex align-items-center justify-content-center bg-light"
                                             style="width:70px;height:70px;font-size:24px;">🛒</div>
                                        @endif
                                        @if(!$inStock)
                                        <span style="position:absolute;top:-6px;right:-6px;background:#dc3545;color:#fff;font-size:9px;font-weight:700;padding:2px 5px;border-radius:6px;">
                                            OUT
                                        </span>
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-600 text-black fs-15 mb-1">{{ $product->name }}</div>
                                        @if($product->category)
                                        <div class="text-muted fs-12">{{ $product->category->name }}</div>
                                        @endif
                                        <div class="text-muted fs-12">
                                            {{ $product->variants->where('is_active', true)->count() }} variant{{ $product->variants->where('is_active', true)->count() != 1 ? 's' : '' }} available
                                        </div>
                                    </div>
                                </a>
                            </td>

                            {{-- Price --}}
                            <td class="text-center p-3">
                                @if($minPrice)
                                    <span class="fw-600 theme3-clr fs-15">
                                        Rs {{ number_format($minPrice, 0) }}
                                        @if($minPrice != $maxPrice)
                                            – Rs {{ number_format($maxPrice, 0) }}
                                        @endif
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>

                            {{-- Availability --}}
                            <td class="text-center p-3">
                                @if($inStock)
                                    <span class="badge bg-success">In Stock</span>
                                    <div class="text-muted fs-11 mt-1">{{ $totalStock }} units</div>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center p-3">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    @if($inStock)
                                        <a href="{{ route('shop.product.details', $product->slug) }}"
                                           class="btn btn-sm theme-btn">
                                            Select &amp; Add to Cart
                                        </a>
                                    @else
                                        <span class="btn btn-sm btn-outline-secondary disabled"
                                              style="cursor:not-allowed;opacity:.6;">
                                            Out of Stock
                                        </span>
                                    @endif
                                    <form action="{{ route('wishlist.remove.product', $item->product_id) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach

                        @else
                        {{-- ── Guest: session-backed items ──────────────── --}}
                        @foreach($items as $item)
                        @php
                            $guestProduct = $item->model ?? null;
                            $guestInStock = $guestProduct && $guestProduct->stock_status !== 'outofstock';
                        @endphp
                        <tr>
                            {{-- Product --}}
                            <td class="p-3">
                                <div class="d-flex align-items-center gap-3">
                                    @if($guestProduct && $guestProduct->image)
                                    <img src="{{ asset('uploads/products/thumbnails/' . $guestProduct->image) }}"
                                         alt="{{ $item->name }}"
                                         class="rounded-2 border"
                                         style="width:70px;height:70px;object-fit:cover;">
                                    @else
                                    <div class="rounded-2 border d-flex align-items-center justify-content-center bg-light"
                                         style="width:70px;height:70px;font-size:24px;">🛒</div>
                                    @endif
                                    <div>
                                        <div class="fw-600 text-black fs-15">{{ $item->name }}</div>
                                        @if($guestProduct?->category)
                                        <div class="text-muted fs-12">{{ $guestProduct->category->name }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="text-center p-3">
                                <span class="fw-600 theme3-clr fs-15">Rs {{ number_format($item->price, 0) }}</span>
                            </td>

                            {{-- Availability --}}
                            <td class="text-center p-3">
                                @if($guestInStock)
                                    <span class="badge bg-success">In Stock</span>
                                @else
                                    <span class="badge bg-danger">Out of Stock</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="text-center p-3">
                                <div class="d-flex gap-2 justify-content-center flex-wrap">
                                    @if($guestInStock)
                                    <form method="POST" action="{{ route('wishlist.move_to_cart', $item->rowId) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm theme-btn">Move to Cart</button>
                                    </form>
                                    @else
                                    <span class="btn btn-sm btn-outline-secondary disabled" style="cursor:not-allowed;opacity:.6;">
                                        Out of Stock
                                    </span>
                                    @endif
                                    <form action="{{ route('wishlist.item.remove', $item->rowId) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn  btn-outline-danger" title="Remove">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @endauth

                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <a href="{{ route('shop.index') }}" class="theme-btn rounded-2">
                    ← Continue Shopping
                </a>
            </div>
        </div>

        @else
        <div class="text-center py-5">
            <div style="font-size:60px;margin-bottom:16px;">🤍</div>
            <h4 class="text-black mb-2">Your wishlist is empty</h4>
            <p class="text-muted mb-4">Save products you love and come back to them anytime.</p>
            <a href="{{ route('shop.index') }}" class="theme-btn">Browse Products</a>
        </div>
        @endif

    </div>
    <img src="{{ asset('assets/img/inner-global-pasta.png') }}" alt="img"
         class="position-absolute bottom-0 pb-100 end-0 float-bob-y mt-4 z-n1 d-sm-block d-none">
</section>

@endsection
