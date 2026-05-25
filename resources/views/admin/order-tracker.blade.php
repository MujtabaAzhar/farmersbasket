@extends('layouts.admin')

@section('content')
@php
    $sc = [
        'ordered'   => 'warning',
        'confirmed' => 'info',
        'packed'    => 'secondary',
        'shipped'   => 'primary',
        'delivered' => 'success',
        'canceled'  => 'danger',
        'returned'  => 'dark',
    ];
    $addr = collect([$order->address, $order->city])->filter()->implode(', ');
@endphp

<style>
    .tl-wrap       { position:relative; padding-left:36px; }
    .tl-wrap::before { content:''; position:absolute; left:12px; top:6px; bottom:6px; width:2px; background:#e5e7eb; }
    .tl-node       { position:relative; margin-bottom:24px; }
    .tl-dot        { position:absolute; left:-29px; top:4px; width:16px; height:16px; border-radius:50%; border:3px solid #fff; box-shadow:0 0 0 2px currentColor; }
    .tl-dot.warning  { background:#fbbf24; color:#fbbf24; }
    .tl-dot.info     { background:#38bdf8; color:#38bdf8; }
    .tl-dot.secondary{ background:#9ca3af; color:#9ca3af; }
    .tl-dot.primary  { background:#6366f1; color:#6366f1; }
    .tl-dot.success  { background:#22c55e; color:#22c55e; }
    .tl-dot.danger   { background:#ef4444; color:#ef4444; }
    .tl-dot.dark     { background:#374151; color:#374151; }
    .tl-note  { font-size:12px; margin-top:4px; color:#374151; background:#f9fafb; border-left:3px solid #e5e7eb; padding:4px 10px; border-radius:0 4px 4px 0; }
    .info-card { background:#f8f9fa; border-radius:10px; padding:18px 22px; }
    .detail-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#6b7280; margin-bottom:3px; }
    .detail-value { font-size:14px; color:#111; }
    .item-row { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #f3f4f6; font-size:13px; }
    .item-row:last-child { border-bottom:none; }
</style>

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- Breadcrumb --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Order Tracker</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.orders') }}"><div class="text-tiny">Orders</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">{{ $order->order_number }}</div></li>
            </ul>
        </div>

        {{-- Header bar --}}
        <div class="wg-box mb-3 d-flex align-items-center justify-between flex-wrap gap-3">
            <div>
                <span class="fw-700 fs-16" style="color:#374151;">{{ $order->order_number }}</span>
                <span class="badge bg-{{ $sc[$order->status] ?? 'secondary' }} ms-2">{{ ucfirst($order->status) }}</span>
                @if($order->source === 'pos')
                    <span class="badge ms-1" style="background:#e8f4fd;color:#1565c0;">POS</span>
                @else
                    <span class="badge ms-1" style="background:#e8f5e9;color:#2e7d32;">Online</span>
                @endif
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}"
                   class="tf-button style-1">
                    <i class="icon-eye"></i> Full Details
                </a>
                <a href="{{ route('admin.orders') }}" class="tf-button" style="background:#eee;color:#333;">
                    ← Back to Orders
                </a>
            </div>
        </div>

        <div class="row g-3">

            {{-- ── Left column: order info ──────────────────────────── --}}
            <div class="col-12 col-lg-5">

                {{-- Customer --}}
                <div class="wg-box mb-3">
                    <h6 class="fw-700 pb-2 mb-3 border-bottom">Customer</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="detail-label">Name</div>
                            <div class="detail-value">{{ $order->user?->name ?? $order->name ?? '—' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Phone</div>
                            <div class="detail-value">{{ $order->user?->mobile ?? $order->phone ?? '—' }}</div>
                        </div>
                        @if($order->source === 'pos' && $order->cashier)
                        <div class="col-6">
                            <div class="detail-label">Cashier</div>
                            <div class="detail-value">{{ $order->cashier->name }}</div>
                        </div>
                        @endif
                        @if($order->branch)
                        <div class="col-6">
                            <div class="detail-label">Branch</div>
                            <div class="detail-value">{{ $order->branch->name }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Order Info --}}
                <div class="wg-box mb-3">
                    <h6 class="fw-700 pb-2 mb-3 border-bottom">Order Info</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="detail-label">Order Date</div>
                            <div class="detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Type</div>
                            <div class="detail-value">{{ ucfirst($order->type ?? 'pickup') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Payment</div>
                            <div class="detail-value">
                                <span class="badge bg-{{ $pm === 'cash' ? 'success' : ($pm ? 'info' : 'secondary') }}">
                                    {{ $pmLabel ?? '—' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Total</div>
                            <div class="detail-value fw-700">Rs {{ number_format($order->total, 2) }}</div>
                        </div>
                        @if($order->courier_name)
                        <div class="col-6">
                            <div class="detail-label">Courier</div>
                            <div class="detail-value">{{ $order->courier_name }}</div>
                        </div>
                        @endif
                        @if($order->tracking_number)
                        <div class="col-6">
                            <div class="detail-label">Tracking No</div>
                            <div class="detail-value">{{ $order->tracking_number }}</div>
                        </div>
                        @endif
                        @if($addr)
                        <div class="col-12">
                            <div class="detail-label">Delivery Address</div>
                            <div class="detail-value">{{ $addr }}</div>
                        </div>
                        @endif
                        @if($order->order_note)
                        <div class="col-12">
                            <div class="detail-label">Order Note</div>
                            <div class="detail-value fst-italic text-muted">{{ $order->order_note }}</div>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Items --}}
                <div class="wg-box mb-3">
                    <h6 class="fw-700 pb-2 mb-3 border-bottom">Items ({{ $order->orderItems->count() }})</h6>
                    @foreach($order->orderItems as $item)
                        <div class="item-row">
                            <span>
                                {{ $item->product?->name ?? 'Product #'.$item->product_id }}
                                @if($item->variant_label ?? null)
                                    <span class="text-muted">({{ $item->variant_label }})</span>
                                @endif
                            </span>
                            <span class="text-muted">
                                ×{{ $item->quantity }}
                                &nbsp;
                                <strong class="text-dark">Rs {{ number_format($item->price * $item->quantity, 0) }}</strong>
                            </span>
                        </div>
                    @endforeach
                </div>

                {{-- Gift Info --}}
                @if($order->giftOrder)
                <div class="wg-box">
                    <h6 class="fw-700 pb-2 mb-3 border-bottom" style="color:#6366f1;">🎁 Gift Details</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="detail-label">Sender</div>
                            <div class="detail-value">{{ $order->giftOrder->sender_name ?? '—' }}</div>
                            <div class="text-tiny text-muted">{{ $order->giftOrder->sender_phone ?? '' }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Receiver</div>
                            <div class="detail-value">{{ $order->giftOrder->receiver_name ?? '—' }}</div>
                            <div class="text-tiny text-muted">{{ $order->giftOrder->receiver_phone ?? '' }}</div>
                        </div>
                        @if($order->giftOrder->receiver_address ?? null)
                        <div class="col-12">
                            <div class="detail-label">Receiver Address</div>
                            <div class="detail-value">{{ $order->giftOrder->receiver_address }}{{ $order->giftOrder->receiver_city ? ', '.$order->giftOrder->receiver_city : '' }}</div>
                        </div>
                        @endif
                        @if($order->giftOrder->gift_message ?? null)
                        <div class="col-12">
                            <div class="detail-label">Gift Message</div>
                            <div class="info-card fst-italic mt-1">{{ $order->giftOrder->gift_message }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif

            </div>

            {{-- ── Right column: timeline ───────────────────────────── --}}
            <div class="col-12 col-lg-7">
                <div class="wg-box">
                    <h6 class="fw-700 pb-2 mb-4 border-bottom">Order Timeline</h6>

                    @if($order->histories->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="icon-clock" style="font-size:36px;opacity:.25;"></i>
                            <div class="mt-2">No history recorded yet.</div>
                        </div>
                    @else
                        <div class="tl-wrap">
                            @foreach($order->histories as $h)
                                @php $color = $sc[$h->status] ?? 'secondary'; @endphp
                                <div class="tl-node">
                                    <div class="tl-dot {{ $color }}"></div>
                                    <div>
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($h->status) }}</span>
                                        <span class="text-tiny text-muted ms-2">
                                            {{ $h->created_at->format('d M Y, h:i A') }}
                                            @if($h->creator)
                                                &nbsp;·&nbsp; by {{ $h->creator->name }}
                                            @endif
                                        </span>
                                        @if($h->note)
                                            <div class="tl-note">{{ $h->note }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
