@extends('layouts.app')
@section('content')

<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h2 class="white-clr fw-semibold text-center heading-font mb-2">Order Confirmation</h2>
            <ul class="breadcrumb align-items-center justify-content-center flex-wrap gap-3">
                <li><a href="{{ route('home.index') }}">Home</a></li>
                <li><i class="fa-solid fa-angle-right"></i></li>
                <li>Order Confirmation</li>
            </ul>
        </div>
    </div>
    <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="img" class="bread-shape-start position-absolute">
    <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="img" class="bread-shape-end position-absolute d-sm-block d-none">
</section>

<section class="section-padding" style="background:#f8f9fa;">
    <div class="container">

        {{-- Success Header --}}
        <div class="text-center mb-5">
            <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3"
                 style="width:80px;height:80px;background:#e8f5e9;border:3px solid #4CAF50;">
                <i class="fa-solid fa-check" style="font-size:36px;color:#4CAF50;"></i>
            </div>
            <h2 class="fw-bold text-dark mb-1">Order Placed Successfully!</h2>
            <p class="text-muted fs-16 mb-0">
                Thank you, <strong>{{ $order->name }}</strong>! We've received your order and will process it shortly.
            </p>
            <span class="badge mt-2 px-3 py-2" style="background:#e8f5e9;color:#2e7d32;font-size:14px;border-radius:20px;">
                Order {{ $order->order_number }}
            </span>
        </div>

        <div class="row g-4">

            {{-- LEFT COLUMN --}}
            <div class="col-lg-8">

                {{-- Order Summary Card --}}
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-receipt me-2" style="color:#4CAF50;"></i> Order Summary
                    </h5>
                    <div class="row g-3">
                        <div class="col-sm-3">
                            <small class="text-muted d-block">Order Number</small>
                            <strong class="text-dark">{{ $order->order_number }}</strong>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted d-block">Date</small>
                            <strong class="text-dark">{{ $order->created_at->format('d M Y') }}</strong>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted d-block">Payment Method</small>
                            <strong class="text-dark">
                                @php
                                    $modeLabels = [
                                        'bank_transfer' => 'Bank Transfer',
                                        'wallet'        => 'JazzCash / EasyPaisa',
                                        'cod'           => 'Cash on Delivery',
                                        'card'          => 'Card Payment',
                                    ];
                                    $mode = $order->transaction?->mode ?? 'N/A';
                                @endphp
                                {{ $modeLabels[$mode] ?? ucfirst($mode) }}
                            </strong>
                        </div>
                        <div class="col-sm-3">
                            <small class="text-muted d-block">Payment Status</small>
                            @php $txStatus = $order->transaction?->status ?? 'pending'; @endphp
                            <span class="badge px-3 py-1 rounded-pill"
                                  style="background:{{ $txStatus === 'approved' ? '#e8f5e9' : '#fff3e0' }};
                                         color:{{ $txStatus === 'approved' ? '#2e7d32' : '#e65100' }};
                                         font-size:12px;">
                                {{ $txStatus === 'approved' ? '✓ Approved' : '⏳ Pending' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Order Items Card --}}
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-box-open me-2" style="color:#4CAF50;"></i> Items Ordered
                    </h5>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <thead style="background:#f8f9fa;">
                                <tr>
                                    <th class="text-muted fw-semibold fs-14 py-2">Product</th>
                                    <th class="text-muted fw-semibold fs-14 py-2 text-center">Qty</th>
                                    <th class="text-muted fw-semibold fs-14 py-2 text-end">Price</th>
                                    <th class="text-muted fw-semibold fs-14 py-2 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->orderItems as $item)
                                <tr class="border-bottom">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            @if($item->product?->image)
                                            <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                                 width="50" height="50" class="rounded-2 border object-fit-cover" alt="{{ $item->product->name }}">
                                            @endif
                                            <div>
                                                <div class="fw-semibold text-dark fs-15">{{ $item->product?->name ?? 'Product' }}</div>
                                                @if($item->variant_label)
                                                    <span class="badge bg-light text-dark border" style="font-size:11px;">
                                                        {{ $item->variant_label }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center fw-semibold">{{ $item->quantity }}</td>
                                    <td class="py-3 text-end">Rs {{ number_format($item->price, 0) }}</td>
                                    <td class="py-3 text-end fw-bold">Rs {{ number_format($item->price * $item->quantity, 0) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Totals --}}
                    <div class="mt-3 pt-2">
                        <div class="d-flex justify-content-end">
                            <div style="min-width:280px;">
                                <div class="d-flex justify-content-between py-1 text-muted fs-14">
                                    <span>Subtotal</span>
                                    <span>Rs {{ number_format($order->subtotal, 0) }}</span>
                                </div>
                                @if($order->discount > 0)
                                <div class="d-flex justify-content-between py-1 text-muted fs-14">
                                    <span>Discount</span>
                                    <span class="text-danger">- Rs {{ number_format($order->discount, 0) }}</span>
                                </div>
                                @endif
                                @if($order->tax > 0)
                                <div class="d-flex justify-content-between py-1 text-muted fs-14">
                                    <span>Tax</span>
                                    <span>Rs {{ number_format($order->tax, 0) }}</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between py-1 text-muted fs-14">
                                    <span>Shipping</span>
                                    <span>Rs {{ number_format(config('cart.shipping', 0), 0) }}</span>
                                </div>
                                <div class="d-flex justify-content-between py-2 mt-1 border-top fw-bold fs-16 text-dark">
                                    <span>Total</span>
                                    <span>Rs {{ number_format($order->total, 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Delivery Details --}}
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                        <i class="fa-solid fa-location-dot me-2" style="color:#4CAF50;"></i>
                        {{ $order->type === 'gift' ? 'Gift Delivery Details' : 'Delivery Details' }}
                    </h5>
                    <div class="row g-2">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Recipient Name</small>
                            <strong>{{ $order->name }}</strong>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Phone</small>
                            <strong>{{ $order->phone }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <small class="text-muted d-block">City</small>
                            <strong>{{ $order->city }}</strong>
                        </div>
                        <div class="col-sm-6 mt-2">
                            <small class="text-muted d-block">Address</small>
                            <strong>{{ $order->address }}</strong>
                        </div>
                        @if($order->type === 'gift' && $order->giftOrder)
                        <div class="col-12 mt-3">
                            <div class="p-3 rounded-2" style="background:#fff9f0;border:1px solid #ffe0b2;">
                                <strong style="color:#e65100;">🎁 Gift Order</strong>
                                <div class="mt-2 fs-14 text-muted">
                                    <div>From: <strong>{{ $order->giftOrder->sender_name }}</strong> ({{ $order->giftOrder->sender_phone }})</div>
                                    @if($order->giftOrder->gift_message)
                                    <div class="mt-1 fst-italic">"{{ $order->giftOrder->gift_message }}"</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="col-lg-4">

                {{-- Order Progress --}}
                <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-4">
                        <i class="fa-solid fa-truck-fast me-2" style="color:#4CAF50;"></i> Order Progress
                    </h5>
                    @php
                        $steps = [
                            ['icon' => 'fa-circle-check', 'label' => 'Order Received',      'done' => true],
                            ['icon' => 'fa-credit-card',  'label' => 'Payment Verification', 'done' => $txStatus === 'approved'],
                            ['icon' => 'fa-box',          'label' => 'Order Packed',         'done' => in_array($order->status, ['packed','shipped','delivered'])],
                            ['icon' => 'fa-truck',        'label' => 'Shipped',              'done' => in_array($order->status, ['shipped','delivered'])],
                            ['icon' => 'fa-house-chimney','label' => 'Delivered',            'done' => $order->status === 'delivered'],
                        ];
                    @endphp
                    <div class="d-flex flex-column gap-3">
                        @foreach($steps as $i => $step)
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle flex-shrink-0"
                                 style="width:36px;height:36px;
                                        background:{{ $step['done'] ? '#e8f5e9' : '#f5f5f5' }};
                                        border:2px solid {{ $step['done'] ? '#4CAF50' : '#ddd' }};">
                                <i class="fa-solid {{ $step['icon'] }} fs-14"
                                   style="color:{{ $step['done'] ? '#4CAF50' : '#bbb' }};"></i>
                            </div>
                            <div>
                                <div class="fs-14 fw-semibold {{ $step['done'] ? 'text-dark' : 'text-muted' }}">
                                    {{ $step['label'] }}
                                </div>
                                @if($step['done'])
                                    <small class="text-success">Completed</small>
                                @elseif($i === 1 && $txStatus !== 'approved')
                                    <small class="text-warning">Awaiting your payment receipt</small>
                                @else
                                    <small class="text-muted">Pending</small>
                                @endif
                            </div>
                        </div>
                        @if(!$loop->last)
                        <div style="width:2px;height:16px;background:#eee;margin-left:17px;"></div>
                        @endif
                        @endforeach
                    </div>
                </div>

                {{-- What's Next Card --}}
                <div class="rounded-3 p-4 mb-4" style="background:#e8f5e9;border:1px solid #c8e6c9;">
                    <h6 class="fw-bold mb-3" style="color:#1b5e20;">📋 What Happens Next?</h6>
                    <ol class="mb-0 ps-3" style="color:#2e7d32;font-size:14px;line-height:2;">
                        <li>We review your order & payment receipt</li>
                        <li>Payment confirmed → order is packed</li>
                        <li>Shipped via courier within <strong>24–48 hrs</strong></li>
                        <li>You receive tracking details on WhatsApp</li>
                    </ol>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex flex-column gap-2">
                    {{-- WhatsApp Button --}}
                    @php
                        $waPhone = '923017147110';
                        $itemLines = '';
                        foreach($order->orderItems as $it) {
                            $label = $it->variant_label ? " ({$it->variant_label})" : '';
                            $itemLines .= "• {$it->product?->name}{$label} x{$it->quantity}\n";
                        }
                        $waMsg = "Assalam-o-Alaikum! 🌿\n\nMy order has been placed:\n\n"
                               . "📦 Order#: {$order->order_number}\n"
                               . "📅 Date: {$order->created_at->format('d M Y')}\n"
                               . "💰 Total: Rs " . number_format($order->total, 0) . "\n"
                               . "💳 Payment: " . ($modeLabels[$mode] ?? ucfirst($mode)) . "\n\n"
                               . $itemLines
                               . "\nPlease confirm my order. Thank you! 🙏";
                        $waLink = "https://wa.me/{$waPhone}?text=" . rawurlencode($waMsg);
                    @endphp
                    <a href="{{ $waLink }}" target="_blank"
                       class="btn d-flex align-items-center justify-content-center gap-2 fw-semibold py-3 rounded-3"
                       style="background:#25D366;color:#fff;font-size:15px;">
                        <i class="fa-brands fa-whatsapp fs-18"></i> Chat on WhatsApp
                    </a>

                    <button onclick="window.print()"
                            class="btn btn-outline-dark d-flex align-items-center justify-content-center gap-2 fw-semibold py-3 rounded-3">
                        <i class="fa-solid fa-print"></i> Print Receipt
                    </button>

                    <a href="{{ route('shop.index') }}"
                       class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 fw-semibold py-3 rounded-3">
                        <i class="fa-solid fa-bag-shopping"></i> Continue Shopping
                    </a>
                </div>

            </div>
        </div>

    </div>
</section>

@endsection

@push('styles')
<style>
@media print {
    .breadcrumb-section,
    .no-print { display: none !important; }
    section.section-padding { padding-top: 0 !important; }
    .shadow-sm { box-shadow: none !important; }
    .col-lg-4 .bg-white:last-of-type,
    .col-lg-4 .d-flex.flex-column { display: none !important; }
}
</style>
@endpush
