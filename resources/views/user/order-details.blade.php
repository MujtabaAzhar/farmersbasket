@extends('layouts.app')
@section('content')

@php
    $statusMap = [
        'ordered'   => ['Pending',   '#fff3e0', '#e65100', 'fa-clock'],
        'confirmed' => ['Confirmed', '#e8f5e9', '#2e7d32', 'fa-circle-check'],
        'packed'    => ['Packed',    '#e3f2fd', '#1565c0', 'fa-box'],
        'shipped'   => ['Shipped',   '#e8eaf6', '#3949ab', 'fa-truck'],
        'delivered' => ['Delivered', '#e8f5e9', '#2e7d32', 'fa-circle-check'],
        'canceled'  => ['Cancelled', '#fce4ec', '#c62828', 'fa-circle-xmark'],
        'returned'  => ['Returned',  '#fff8e1', '#f57f17', 'fa-rotate-left'],
    ];
    [$statusLabel, $statusBg, $statusColor, $statusIcon] = $statusMap[$order->status] ?? [ucfirst($order->status), '#f5f5f5', '#555', 'fa-circle'];

    $modeLabels = [
        'bank_transfer' => 'Bank Transfer',
        'wallet'        => 'JazzCash / EasyPaisa',
        'cod'           => 'Cash on Delivery',
        'card'          => 'Card Payment',
    ];
@endphp

{{-- Breadcrumb --}}
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">Order Details</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li><a href="{{ route('user.index') }}">Dashboard</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li><a href="{{ route('user.orders') }}">My Orders</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>{{ $order->order_number }}</li>
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

            {{-- Content --}}
            <div class="col-lg-9">

                {{-- Page Header --}}
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
                    <div>
                        <h5 class="fw-bold text-dark mb-0">{{ $order->order_number }}</h5>
                        <p class="text-muted fs-13 mb-0">Placed on {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge px-3 py-2 rounded-pill fw-semibold d-flex align-items-center gap-1"
                              style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:13px;">
                            <i class="fa-solid {{ $statusIcon }}"></i> {{ $statusLabel }}
                        </span>
                        <a href="{{ route('user.orders') }}"
                           class="btn btn-sm rounded-2 fw-semibold"
                           style="background:#f5f5f5;color:#555;border:1px solid #ddd;font-size:13px;">
                            <i class="fa-solid fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
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

                <div class="row g-4">

                    {{-- LEFT: Items + Timeline --}}
                    <div class="col-lg-8">

                        {{-- Order Items --}}
                        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-box-open me-2" style="color:#4CAF50;"></i>
                                Items Ordered
                            </h6>

                            @foreach($orderItems as $item)
                            <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'border-bottom pb-3 mb-3' : '' }}">
                                @if($item->product?->image)
                                <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                     width="68" height="68"
                                     class="rounded-2 border object-fit-cover flex-shrink-0"
                                     alt="{{ $item->product->name }}">
                                @else
                                <div class="d-flex align-items-center justify-content-center rounded-2 border flex-shrink-0"
                                     style="width:68px;height:68px;background:#f5f5f5;">
                                    <i class="fa-solid fa-image text-muted"></i>
                                </div>
                                @endif
                                <div class="flex-grow-1">
                                    <div class="fw-semibold text-dark fs-15">
                                        @if($item->product)
                                        <a href="{{ route('shop.product.details', $item->product->slug) }}"
                                           class="text-dark text-decoration-none" target="_blank">
                                            {{ $item->product->name }}
                                        </a>
                                        @else
                                        Product
                                        @endif
                                    </div>
                                    @if($item->variant_label)
                                    <span class="badge bg-light text-dark border mt-1" style="font-size:11px;">
                                        {{ $item->variant_label }}
                                    </span>
                                    @endif
                                    <div class="text-muted fs-13 mt-1">
                                        Rs {{ number_format($item->price, 0) }} × {{ $item->quantity }}
                                    </div>
                                </div>
                                <div class="fw-bold text-dark fs-15 flex-shrink-0">
                                    Rs {{ number_format($item->price * $item->quantity, 0) }}
                                </div>
                            </div>
                            @endforeach

                            @if($orderItems->hasPages())
                            <div class="mt-3">{{ $orderItems->links('pagination::bootstrap-5') }}</div>
                            @endif
                        </div>

                        {{-- Order Timeline --}}
                        @if($order->histories->where('is_admin_note', false)->count())
                        <div class="bg-white rounded-3 shadow-sm p-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-4">
                                <i class="fa-solid fa-timeline me-2" style="color:#4CAF50;"></i>
                                Order Timeline
                            </h6>
                            <div class="d-flex flex-column gap-0">
                                @foreach($order->histories->where('is_admin_note', false) as $history)
                                @php
                                    [$hLabel, $hBg, $hColor, $hIcon] = $statusMap[$history->status] ?? [ucfirst($history->status), '#f5f5f5', '#888', 'fa-circle'];
                                @endphp
                                <div class="d-flex gap-3 position-relative {{ !$loop->last ? 'pb-4' : '' }}">
                                    @if(!$loop->last)
                                    <div class="position-absolute" style="left:17px;top:36px;bottom:0;width:2px;background:#e0e0e0;"></div>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0 z-1"
                                         style="width:36px;height:36px;background:{{ $hBg }};border:2px solid {{ $hColor }};">
                                        <i class="fa-solid {{ $hIcon }} fs-12" style="color:{{ $hColor }};"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark fs-14">{{ $hLabel }}</div>
                                        <div class="text-muted fs-12">{{ $history->created_at->format('d M Y, h:i A') }}</div>
                                        @if($history->note)
                                        <div class="text-muted fs-13 mt-1">{{ $history->note }}</div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>

                    {{-- RIGHT: Summary cards --}}
                    <div class="col-lg-4">

                        {{-- Order Summary --}}
                        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-receipt me-2" style="color:#4CAF50;"></i>
                                Order Summary
                            </h6>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between fs-14">
                                    <span class="text-muted">Subtotal</span>
                                    <span class="fw-semibold">Rs {{ number_format($order->subtotal, 0) }}</span>
                                </div>
                                @if($order->discount > 0)
                                <div class="d-flex justify-content-between fs-14">
                                    <span class="text-muted">Discount</span>
                                    <span class="fw-semibold text-danger">− Rs {{ number_format($order->discount, 0) }}</span>
                                </div>
                                @endif
                                @if($order->tax > 0)
                                <div class="d-flex justify-content-between fs-14">
                                    <span class="text-muted">Tax</span>
                                    <span class="fw-semibold">Rs {{ number_format($order->tax, 0) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between fs-14 border-top pt-2 mt-1">
                                    <span class="fw-bold text-dark">Total</span>
                                    <span class="fw-bold fs-16" style="color:#2d6a2d;">Rs {{ number_format($order->total, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Info --}}
                        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-credit-card me-2" style="color:#4CAF50;"></i>
                                Payment
                            </h6>
                            @if($transection)
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex justify-content-between fs-14">
                                    <span class="text-muted">Method</span>
                                    <span class="fw-semibold">{{ $modeLabels[$transection->mode] ?? ucfirst($transection->mode) }}</span>
                                </div>
                                <div class="d-flex justify-content-between fs-14 align-items-center">
                                    <span class="text-muted">Status</span>
                                    @php
                                        $txColors = [
                                            'approved' => ['#e8f5e9','#2e7d32','Approved'],
                                            'declined' => ['#fce4ec','#c62828','Declined'],
                                            'refunded' => ['#f3e5f5','#6a1b9a','Refunded'],
                                            'pending'  => ['#fff3e0','#e65100','Pending'],
                                        ];
                                        [$txBg,$txColor,$txLabel] = $txColors[$transection->status] ?? ['#f5f5f5','#555',ucfirst($transection->status)];
                                    @endphp
                                    <span class="badge px-2 py-1 rounded-pill fw-semibold"
                                          style="background:{{ $txBg }};color:{{ $txColor }};font-size:11px;">
                                        {{ $txLabel }}
                                    </span>
                                </div>
                                @if($transection->payment_receipt)
                                <div class="d-flex justify-content-between fs-14 align-items-center">
                                    <span class="text-muted">Receipt</span>
                                    <a href="{{ asset('uploads/payment_receipts/' . $transection->payment_receipt) }}"
                                       target="_blank" class="fs-12 fw-semibold text-decoration-none" style="color:#2d6a2d;">
                                        <i class="fa-solid fa-file-image me-1"></i> View
                                    </a>
                                </div>
                                @endif
                            </div>
                            @else
                            <p class="text-muted fs-13 mb-0">No payment record found.</p>
                            @endif
                        </div>

                        {{-- Delivery Address --}}
                        <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="fa-solid fa-location-dot me-2" style="color:#4CAF50;"></i>
                                Delivery Address
                            </h6>
                            <div class="d-flex flex-column gap-1 fs-14">
                                <div class="fw-semibold text-dark">{{ $order->name }}</div>
                                <div class="text-muted">{{ $order->phone }}</div>
                                <div class="text-muted mt-1">{{ $order->address }}</div>
                                <div class="text-muted">{{ $order->city }}@if($order->state), {{ $order->state }}@endif</div>
                                @if($order->tracking_number)
                                <div class="mt-2 pt-2 border-top">
                                    <span class="text-muted">Tracking: </span>
                                    <span class="fw-semibold text-dark">{{ $order->tracking_number }}</span>
                                    @if($order->courier_name)
                                    <span class="text-muted"> ({{ $order->courier_name }})</span>
                                    @endif
                                </div>
                                @endif
                                @if($order->estimated_delivery_date && !in_array($order->status, ['delivered','canceled','returned']))
                                <div class="fs-13">
                                    <span class="text-muted">Est. Delivery: </span>
                                    <span class="fw-semibold" style="color:#2d6a2d;">
                                        {{ \Carbon\Carbon::parse($order->estimated_delivery_date)->format('d M Y') }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Cancel Order --}}
                        @if($order->status === 'ordered')
                        <form action="{{ route('user.order.cancel') }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to cancel this order?');">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="order_id" value="{{ $order->id }}">
                            <button type="submit" class="btn w-100 rounded-3 fw-semibold py-3"
                                    style="background:#fce4ec;color:#c62828;border:1px solid #f8bbd0;font-size:14px;">
                                <i class="fa-solid fa-xmark me-2"></i> Cancel Order
                            </button>
                        </form>
                        @endif

                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
