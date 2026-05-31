@extends('layouts.app')
@section('content')

{{-- Breadcrumb --}}
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">My Account</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>Dashboard</li>
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

            {{-- Main Content --}}
            <div class="col-lg-9">

                {{-- Welcome Banner --}}
                <div class="rounded-3 p-4 mb-4 d-flex align-items-center gap-4"
                     style="background:linear-gradient(135deg,#1a3a1a,#2d6a2d);">
                    <div class="d-none d-sm-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                         style="width:64px;height:64px;background:rgba(255,255,255,0.15);border:2px solid rgba(255,255,255,0.3);">
                        <span style="font-size:26px;font-weight:700;color:#fff;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <h4 class="text-white fw-bold mb-1">Hello, {{ Auth::user()->name }}! 👋</h4>
                        <p class="mb-0 fs-14" style="color:rgba(255,255,255,0.75);">
                            Welcome to your account dashboard. View orders, track deliveries, and manage your details.
                        </p>
                    </div>
                </div>

                {{-- Stats Cards --}}
                <div class="row g-3 mb-4">
                    @php
                        $cards = [
                            ['label' => 'Total Orders',  'value' => $stats['total'],     'icon' => 'fa-bag-shopping',       'color' => '#2d6a2d', 'bg' => '#e8f5e9'],
                            ['label' => 'Pending',        'value' => $stats['pending'],   'icon' => 'fa-clock',              'color' => '#e65100', 'bg' => '#fff3e0'],
                            ['label' => 'Delivered',      'value' => $stats['delivered'], 'icon' => 'fa-circle-check',       'color' => '#1565c0', 'bg' => '#e3f2fd'],
                            ['label' => 'Total Spent',    'value' => 'Rs ' . number_format($stats['spent'], 0), 'icon' => 'fa-wallet', 'color' => '#6a1b9a', 'bg' => '#f3e5f5'],
                        ];
                    @endphp
                    @foreach($cards as $card)
                    <div class="col-sm-6 col-xl-3">
                        <div class="bg-white rounded-3 shadow-sm p-4 h-100">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center justify-content-center rounded-2"
                                     style="width:44px;height:44px;background:{{ $card['bg'] }};">
                                    <i class="fa-solid {{ $card['icon'] }} fs-18" style="color:{{ $card['color'] }};"></i>
                                </div>
                            </div>
                            <div class="fw-bold fs-22 text-dark mb-1">{{ $card['value'] }}</div>
                            <div class="text-muted fs-13">{{ $card['label'] }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- Recent Orders --}}
                <div class="bg-white rounded-3 shadow-sm p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fa-solid fa-clock-rotate-left me-2" style="color:#4CAF50;"></i> Recent Orders
                        </h5>
                        <a href="{{ route('user.orders') }}" class="fs-13 fw-semibold text-decoration-none" style="color:#2d6a2d;">
                            View All <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>

                    @if($recentOrders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead>
                                <tr style="background:#f8f9fa;border-radius:8px;">
                                    <th class="text-muted fw-semibold fs-13 py-2 ps-3">Order</th>
                                    <th class="text-muted fw-semibold fs-13 py-2">Date</th>
                                    <th class="text-muted fw-semibold fs-13 py-2 text-center">Items</th>
                                    <th class="text-muted fw-semibold fs-13 py-2 text-end">Total</th>
                                    <th class="text-muted fw-semibold fs-13 py-2 text-center">Status</th>
                                    <th class="text-muted fw-semibold fs-13 py-2 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentOrders as $order)
                                @php
                                    $statusMap = [
                                        'ordered'   => ['Pending',   '#fff3e0', '#e65100'],
                                        'confirmed' => ['Confirmed', '#e8f5e9', '#2e7d32'],
                                        'packed'    => ['Packed',    '#e3f2fd', '#1565c0'],
                                        'shipped'   => ['Shipped',   '#e8eaf6', '#3949ab'],
                                        'delivered' => ['Delivered', '#e8f5e9', '#2e7d32'],
                                        'canceled'  => ['Cancelled', '#fce4ec', '#c62828'],
                                    ];
                                    [$statusLabel, $statusBg, $statusColor] = $statusMap[$order->status] ?? [ucfirst($order->status), '#f5f5f5', '#555'];
                                @endphp
                                <tr class="border-bottom">
                                    <td class="py-3 ps-3">
                                        <div class="fw-semibold text-dark fs-14">{{ $order->order_number }}</div>
                                        <div class="text-muted fs-12">{{ $order->city }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fs-14 text-dark">{{ $order->created_at->format('d M Y') }}</div>
                                        <div class="text-muted fs-12">{{ $order->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="fw-semibold fs-14">{{ $order->orderItems->count() }}</span>
                                    </td>
                                    <td class="py-3 text-end fw-bold fs-14" style="color:#2d6a2d;">
                                        Rs {{ number_format($order->total, 0) }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge px-3 py-1 rounded-pill fw-semibold"
                                              style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:12px;">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <a href="{{ route('user.order.details', $order->id) }}"
                                           class="btn btn-sm rounded-2 fw-semibold"
                                           style="background:#e8f5e9;color:#2d6a2d;font-size:12px;border:1px solid #c8e6c9;">
                                            <i class="fa-solid fa-eye me-1"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                             style="width:72px;height:72px;background:#f5f5f5;">
                            <i class="fa-solid fa-bag-shopping fs-28 text-muted"></i>
                        </div>
                        <h6 class="text-muted fw-semibold">No orders yet</h6>
                        <p class="text-muted fs-14 mb-3">You haven't placed any orders yet.</p>
                        <a href="{{ route('shop.index') }}" class="theme-btn px-4">Shop Now</a>
                    </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
