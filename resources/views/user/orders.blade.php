@extends('layouts.app')
@section('content')

{{-- Breadcrumb --}}
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">My Orders</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li><a href="{{ route('user.index') }}">Dashboard</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>My Orders</li>
            </ul>
        </div>
    </div>
    <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="" class="bread-shape-start position-absolute">
    <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="" class="bread-shape-end position-absolute d-sm-block d-none">
</section>

<section class="section-padding" style="background:#f5f6f8;">
    <div class="container">
        <div class="row g-4 align-items-start">

            {{-- Sidebar --}}
            <div class="col-lg-3">
                @include('user.account-nav')
            </div>

            {{-- Orders Content --}}
            <div class="col-lg-9">

                {{-- Page Header --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">My Orders</h5>
                        <p class="text-muted fs-13 mb-0">Track and manage all your orders</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="theme-btn px-4" style="font-size:13px;">
                        <i class="fa-solid fa-bag-shopping me-1"></i> Continue Shopping
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
                         style="background:#e8f5e9;color:#2e7d32;">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert border-0 rounded-3 mb-4 d-flex align-items-center gap-2"
                         style="background:#fce4ec;color:#c62828;">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif

                @if($orders->count() > 0)

                    {{-- Orders List --}}
                    <div class="d-flex flex-column gap-3">
                        @foreach($orders as $order)
                        @php
                            $statusMap = [
                                'ordered'   => ['Pending',    '#fff3e0', '#e65100', 'fa-clock'],
                                'confirmed' => ['Confirmed',  '#e8f5e9', '#2e7d32', 'fa-circle-check'],
                                'packed'    => ['Packed',     '#e3f2fd', '#1565c0', 'fa-box'],
                                'shipped'   => ['Shipped',    '#e8eaf6', '#3949ab', 'fa-truck'],
                                'delivered' => ['Delivered',  '#e8f5e9', '#2e7d32', 'fa-circle-check'],
                                'canceled'  => ['Cancelled',  '#fce4ec', '#c62828', 'fa-circle-xmark'],
                            ];
                            [$statusLabel, $statusBg, $statusColor, $statusIcon] = $statusMap[$order->status] ?? [ucfirst($order->status), '#f5f5f5', '#555', 'fa-circle'];

                            $modeLabels = [
                                'bank_transfer' => 'Bank Transfer',
                                'wallet'        => 'JazzCash / EasyPaisa',
                                'cod'           => 'Cash on Delivery',
                                'card'          => 'Card',
                            ];
                            $payMode = $order->transaction?->mode ?? null;
                        @endphp

                        <div class="bg-white rounded-3 shadow-sm overflow-hidden">

                            {{-- Order Header --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 px-4 py-3 border-bottom"
                                 style="background:#fafafa;">
                                <div class="d-flex align-items-center gap-3 flex-wrap">
                                    <div>
                                        <span class="text-muted fs-12">Order Number</span>
                                        <div class="fw-bold text-dark fs-15">{{ $order->order_number }}</div>
                                    </div>
                                    <div class="d-none d-sm-block" style="width:1px;height:32px;background:#e0e0e0;"></div>
                                    <div>
                                        <span class="text-muted fs-12">Placed On</span>
                                        <div class="fw-semibold text-dark fs-14">{{ $order->created_at->format('d M Y') }}</div>
                                    </div>
                                    <div class="d-none d-sm-block" style="width:1px;height:32px;background:#e0e0e0;"></div>
                                    <div>
                                        <span class="text-muted fs-12">Items</span>
                                        <div class="fw-semibold text-dark fs-14">{{ $order->orderItems->count() }} item(s)</div>
                                    </div>
                                    @if($payMode)
                                    <div class="d-none d-md-block" style="width:1px;height:32px;background:#e0e0e0;"></div>
                                    <div class="d-none d-md-block">
                                        <span class="text-muted fs-12">Payment</span>
                                        <div class="fw-semibold text-dark fs-14">{{ $modeLabels[$payMode] ?? ucfirst($payMode) }}</div>
                                    </div>
                                    @endif
                                </div>
                                <span class="badge px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1"
                                      style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:12px;">
                                    <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusLabel }}
                                </span>
                            </div>

                            {{-- Order Items Preview --}}
                            <div class="px-4 py-3">
                                @foreach($order->orderItems->take(2) as $item)
                                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3' : '' }}">
                                    @if($item->product?->image)
                                    <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                         width="56" height="56" class="rounded-2 border object-fit-cover flex-shrink-0"
                                         alt="{{ $item->product->name }}">
                                    @else
                                    <div class="d-flex align-items-center justify-content-center rounded-2 border flex-shrink-0"
                                         style="width:56px;height:56px;background:#f5f5f5;">
                                        <i class="fa-solid fa-image text-muted"></i>
                                    </div>
                                    @endif
                                    <div class="flex-grow-1 min-w-0">
                                        <div class="fw-semibold text-dark fs-14 text-truncate">
                                            {{ $item->product?->name ?? 'Product' }}
                                        </div>
                                        @if($item->variant_label)
                                        <span class="badge bg-light text-dark border" style="font-size:11px;">
                                            {{ $item->variant_label }}
                                        </span>
                                        @endif
                                        <div class="text-muted fs-13">Qty: {{ $item->quantity }}</div>
                                    </div>
                                    <div class="fw-bold text-dark fs-14 flex-shrink-0">
                                        Rs {{ number_format($item->price * $item->quantity, 0) }}
                                    </div>
                                </div>
                                @endforeach

                                @if($order->orderItems->count() > 2)
                                <p class="text-muted fs-13 mb-0 mt-2">
                                    + {{ $order->orderItems->count() - 2 }} more item(s)
                                </p>
                                @endif
                            </div>

                            {{-- Order Footer --}}
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-4 py-3 border-top"
                                 style="background:#fafafa;">
                                <div class="d-flex align-items-center gap-4 flex-wrap">
                                    <div>
                                        <span class="text-muted fs-12">Order Total</span>
                                        <div class="fw-bold fs-16" style="color:#2d6a2d;">
                                            Rs {{ number_format($order->total, 0) }}
                                        </div>
                                    </div>
                                    @if($order->status === 'delivered' && $order->delivered_date)
                                    <div>
                                        <span class="text-muted fs-12">Delivered On</span>
                                        <div class="fw-semibold text-dark fs-14">
                                            {{ \Carbon\Carbon::parse($order->delivered_date)->format('d M Y') }}
                                        </div>
                                    </div>
                                    @endif
                                    @if($order->tracking_number)
                                    <div>
                                        <span class="text-muted fs-12">Tracking</span>
                                        <div class="fw-semibold text-dark fs-14">{{ $order->tracking_number }}</div>
                                    </div>
                                    @endif
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    @if($order->status === 'ordered')
                                    <form action="{{ route('user.order.cancel') }}" method="POST"
                                          onsubmit="return confirm('Cancel this order?');">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <button type="submit" class="btn btn-sm rounded-2 fw-semibold"
                                                style="background:#fce4ec;color:#c62828;border:1px solid #f8bbd0;font-size:12px;">
                                            <i class="fa-solid fa-xmark me-1"></i> Cancel Order
                                        </button>
                                    </form>
                                    @endif
                                    <a href="{{ route('user.order.details', $order->id) }}"
                                       class="btn btn-sm rounded-2 fw-semibold"
                                       style="background:#e8f5e9;color:#2d6a2d;border:1px solid #c8e6c9;font-size:12px;">
                                        <i class="fa-solid fa-eye me-1"></i> View Details
                                    </a>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($orders->hasPages())
                    <div class="mt-4">
                        {{ $orders->links('pagination::bootstrap-5') }}
                    </div>
                    @endif

                @else
                    {{-- Empty State --}}
                    <div class="bg-white rounded-3 shadow-sm text-center py-5 px-4">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4"
                             style="width:80px;height:80px;background:#f5f5f5;">
                            <i class="fa-solid fa-bag-shopping fs-32 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">No Orders Yet</h5>
                        <p class="text-muted fs-15 mb-4">
                            You haven't placed any orders yet.<br>Start shopping to see your orders here.
                        </p>
                        <a href="{{ route('shop.index') }}" class="theme-btn px-5">
                            <i class="fa-solid fa-bag-shopping me-2"></i> Shop Now
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</section>

@endsection
