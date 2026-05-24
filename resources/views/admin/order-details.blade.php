@extends('layouts.admin')
@section('content')
<style>
    .timeline { position: relative; padding-left: 2rem; }
    .timeline::before { content: ''; position: absolute; left: .55rem; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
    .timeline-item { position: relative; margin-bottom: 1.25rem; }
    .timeline-item::before { content: ''; position: absolute; left: -1.5rem; top: .3rem; width: 12px; height: 12px; border-radius: 50%; background: #6c757d; border: 2px solid #fff; box-shadow: 0 0 0 2px #6c757d; }
    .timeline-item.done::before   { background: #28a745; box-shadow: 0 0 0 2px #28a745; }
    .timeline-item.canceled::before { background: #dc3545; box-shadow: 0 0 0 2px #dc3545; }
    .timeline-item.returned::before { background: #fd7e14; box-shadow: 0 0 0 2px #fd7e14; }
    .detail-label { font-weight: 600; color: #555; font-size: 12px; text-transform: uppercase; letter-spacing: .4px; }
    .detail-value { font-size: 14px; color: #222; }
    .info-card { background: #f8f9fa; border-radius: 8px; padding: 16px 20px; }
    .source-pos    { background:#e8f4fd; color:#1565c0; }
    .source-online { background:#e8f5e9; color:#2e7d32; }
</style>

@php
    $statusColors   = ['ordered'=>'warning','confirmed'=>'info','packed'=>'secondary','shipped'=>'primary','delivered'=>'success','canceled'=>'danger','returned'=>'dark'];
    $payColors      = ['pending'=>'warning','paid'=>'success','failed'=>'danger','refunded'=>'secondary'];
    $isPOS          = ($order->source === 'pos');
    $isPickupType   = $isPOS && $order->type === 'pickup';
    $isBookingType  = $isPOS && $order->type === 'booking';
    $isGiftType     = $isPOS && $order->type === 'gift';
    $canQuickPickup = $isPickupType && !in_array($order->status, ['delivered','canceled','returned']);
@endphp

<div class="main-content-inner">
    <div class="main-content-wrap">

        {{-- ── Page header ─────────────────────────────────────────────── --}}
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <div class="d-flex align-items-center gap-3">
                <h3 class="mb-0">{{ $order->order_number }}</h3>
                <span class="badge px-3 py-1 {{ $isPOS ? 'source-pos' : 'source-online' }}" style="font-size:13px;border-radius:20px;">
                    {{ $isPOS ? '🖥 POS' : '🌐 Online' }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                @if(in_array($order->status, ['confirmed','packed','shipped']) && !$existingShipment)
                <a href="{{ route('admin.shipments.create', ['order_id' => $order->id]) }}"
                   class="tf-button style-1" style="font-size:13px;padding:6px 14px;">
                    🚚 Create Shipment
                </a>
                @elseif($existingShipment)
                <a href="{{ route('admin.shipments.show', $existingShipment) }}"
                   class="tf-button" style="font-size:13px;padding:6px 14px;background:#17a2b8;color:#fff;">
                    📦 View Shipment
                </a>
                @endif
                <a href="{{ route('admin.shipments.index') }}" style="font-size:13px;">View All Shipments</a>
            </div>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.orders') }}"><div class="text-tiny">Orders</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">{{ $order->order_number }}</div></li>
            </ul>
        </div>

        @if(Session::has('status'))
            <div class="alert alert-success mb-4">{{ Session::get('status') }}</div>
        @endif

        {{-- ── Row 1: Summary + Status ─────────────────────────────────── --}}
        <div class="row g-4 mb-4">

            {{-- Order summary --}}
            <div class="col-md-6">
                <div class="wg-box h-100">
                    <h5 class="mb-3">Order Summary</h5>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="detail-label">Order Date</div>
                            <div class="detail-value">{{ $order->created_at->format('d M Y, H:i') }}</div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Order Status</div>
                            <div class="detail-value">
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Payment Status</div>
                            <div class="detail-value">
                                <span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }}">{{ ucfirst($order->payment_status) }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="detail-label">Coupon</div>
                            <div class="detail-value">{{ $order->coupon_code ?: '—' }}</div>
                        </div>
                        @if($order->delivered_date)
                        <div class="col-6">
                            <div class="detail-label">Delivered</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($order->delivered_date)->format('d M Y') }}</div>
                        </div>
                        @endif
                        @if($order->canceled_date)
                        <div class="col-6">
                            <div class="detail-label">Canceled</div>
                            <div class="detail-value">{{ \Carbon\Carbon::parse($order->canceled_date)->format('d M Y') }}</div>
                        </div>
                        @endif
                        @if($order->order_note)
                        <div class="col-12 mt-1">
                            <div class="detail-label">Order Note</div>
                            <div class="detail-value">{{ $order->order_note }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Financial summary --}}
            <div class="col-md-6">
                <div class="wg-box h-100">
                    <h5 class="mb-3">Financials</h5>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="detail-label ps-0">Subtotal</td>
                            <td class="text-end fw-600">Rs {{ number_format($order->subtotal, 2) }}</td>
                        </tr>
                        @if($order->discount > 0)
                        <tr>
                            <td class="detail-label ps-0">Discount</td>
                            <td class="text-end text-danger">− Rs {{ number_format($order->discount, 2) }}</td>
                        </tr>
                        @endif
                        @if($order->tax > 0)
                        <tr>
                            <td class="detail-label ps-0">Tax</td>
                            <td class="text-end">Rs {{ number_format($order->tax, 2) }}</td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td class="detail-label ps-0 pt-2" style="font-size:14px;">Total</td>
                            <td class="text-end fw-bold pt-2" style="font-size:16px;">Rs {{ number_format($order->total, 2) }}</td>
                        </tr>
                    </table>

                    {{-- Payment info --}}
                    <div class="mt-3 pt-3 border-top">
                        @if($isPOS && $order->posPayment)
                            @php $pp = $order->posPayment; @endphp
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="detail-label">Payment Method</div>
                                    <div class="detail-value text-capitalize">{{ $pp->method ?? '—' }}</div>
                                </div>
                                @if($pp->method === 'cash')
                                <div class="col-6">
                                    <div class="detail-label">Cash Received</div>
                                    <div class="detail-value">Rs {{ number_format($pp->cash_received, 2) }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-label">Change Given</div>
                                    <div class="detail-value">Rs {{ number_format($pp->change_given, 2) }}</div>
                                </div>
                                @endif
                                @if($pp->reference_no)
                                <div class="col-6">
                                    <div class="detail-label">Reference No</div>
                                    <div class="detail-value">{{ $pp->reference_no }}</div>
                                </div>
                                @endif
                            </div>
                        @elseif(!$isPOS && $order->transaction)
                            @php $tx = $order->transaction; @endphp
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="detail-label">Payment Mode</div>
                                    <div class="detail-value text-capitalize">{{ $tx->mode ?? '—' }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-label">Transaction Status</div>
                                    <div class="detail-value">
                                        @php $ts = $tx->status ?? 'pending'; @endphp
                                        <span class="badge bg-{{ $ts === 'approved' ? 'success' : ($ts === 'declined' ? 'danger' : ($ts === 'refunded' ? 'secondary' : 'warning')) }}">{{ ucfirst($ts) }}</span>
                                    </div>
                                </div>
                            </div>
                        @else
                            <span class="text-muted small">No payment record found.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Row 2: POS Info  OR  Delivery Address ───────────────────── --}}
        @if($isPOS)
        <div class="wg-box mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <h5 class="mb-0">POS Order Info</h5>
                @if($isPickupType)
                    <span style="background:#e3f2fd;color:#1565c0;border:1px solid #bbdefb;border-radius:20px;padding:4px 14px;font-size:13px;font-weight:700;">
                        🏪 Store Pickup
                    </span>
                @elseif($isBookingType)
                    <span style="background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9;border-radius:20px;padding:4px 14px;font-size:13px;font-weight:700;">
                        🚚 Delivery Booking
                    </span>
                @elseif($isGiftType)
                    <span style="background:#fff3e0;color:#e65100;border:1px solid #ffcc80;border-radius:20px;padding:4px 14px;font-size:13px;font-weight:700;">
                        🎁 Gift Delivery
                    </span>
                @endif
            </div>

            <div class="row g-3">
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="detail-label mb-1">Cashier</div>
                        <div class="detail-value">{{ $order->cashier?->name ?? '—' }}</div>
                        <div class="text-muted small">{{ $order->cashier?->email }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="detail-label mb-1">Branch</div>
                        <div class="detail-value">{{ $order->branch?->name ?? '—' }}</div>
                        <div class="text-muted small">{{ $order->branch?->city }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="detail-label mb-1">Customer</div>
                        <div class="detail-value">{{ $order->name ?: 'Walk-in' }}</div>
                        <div class="text-muted small">{{ $order->phone ?: '—' }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="info-card">
                        <div class="detail-label mb-1">Session</div>
                        <div class="detail-value">#{{ $order->pos_session_id ?? '—' }}</div>
                        @if($order->requested_delivery_date)
                        <div class="text-muted small">Req: {{ \Carbon\Carbon::parse($order->requested_delivery_date)->format('d M Y') }}</div>
                        @endif
                    </div>
                </div>

                {{-- Booking: show full delivery address --}}
                @if($isBookingType)
                <div class="col-12">
                    <div style="background:#f0fdf4;border:1px solid #c8e6c9;border-radius:8px;padding:14px 18px;">
                        <div class="detail-label mb-2" style="color:#2e7d32;">📍 Delivery Address</div>
                        <div class="row g-2">
                            <div class="col-md-8">
                                <div class="detail-value fw-500">{{ $order->address ?: '—' }}</div>
                                @if($order->locality)
                                <div class="text-muted small">{{ $order->locality }}</div>
                                @endif
                            </div>
                            <div class="col-md-2">
                                <div class="detail-label">City</div>
                                <div class="detail-value">{{ $order->city ?: '—' }}</div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">{{ $order->phone ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                {{-- Pickup: show branch address as collection point --}}
                @if($isPickupType)
                <div class="col-12">
                    <div style="background:#e3f2fd;border:1px solid #bbdefb;border-radius:8px;padding:14px 18px;">
                        <div class="detail-label mb-2" style="color:#1565c0;">🏪 Collection Point</div>
                        <div class="detail-value">{{ $order->branch?->name ?? 'Branch' }}</div>
                        <div class="text-muted small">{{ $order->branch?->address ?? $order->address }}</div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── Quick Pickup Action (only for pending pickup orders) ───── --}}
        @if($canQuickPickup)
        <div class="wg-box mb-4" style="border:2px solid #1976d2;background:#f0f8ff;">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div>
                    <h5 class="mb-1" style="color:#1565c0;">🏪 Customer Pickup</h5>
                    <p class="mb-0 text-muted small">
                        Current status: <strong>{{ ucfirst($order->status) }}</strong>.
                        Once the customer collects their order from the counter, mark it as picked up.
                    </p>
                </div>
                <form action="{{ route('admin.order.status.update') }}" method="POST" class="d-inline-block"
                      onsubmit="return confirm('Mark this order as Picked Up by customer?')">
                    @csrf @method('PUT')
                    <input type="hidden" name="order_id"     value="{{ $order->id }}">
                    <input type="hidden" name="order_status" value="delivered">
                    <input type="hidden" name="admin_note"   value="Picked up by customer at {{ $order->branch?->name ?? 'store' }}.">
                    <button type="submit" class="tf-button style-1" style="background:#1976d2;border-color:#1976d2;padding:10px 24px;font-size:14px;">
                        ✓ Mark as Picked Up
                    </button>
                </form>
            </div>
        </div>
        @endif

        @else
        <div class="wg-box mb-4">
            <h5 class="mb-3">Delivery Address</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="detail-label">Name</div>
                                <div class="detail-value">{{ $order->name }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">{{ $order->phone }}</div>
                            </div>
                            <div class="col-12">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">{{ $order->address }}{{ $order->locality ? ', ' . $order->locality : '' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">City / State</div>
                                <div class="detail-value">{{ $order->city }}{{ $order->state ? ', ' . $order->state : '' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Country / ZIP</div>
                                <div class="detail-value">{{ $order->country }}{{ $order->zip ? ' — ' . $order->zip : '' }}</div>
                            </div>
                            @if($order->landmark)
                            <div class="col-12">
                                <div class="detail-label">Landmark</div>
                                <div class="detail-value">{{ $order->landmark }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @if($order->tracking_number || $order->courier_name || $order->estimated_delivery_date)
                <div class="col-md-6">
                    <div class="info-card">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="detail-label">Courier</div>
                                <div class="detail-value">{{ $order->courier_name ?: '—' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Tracking #</div>
                                <div class="detail-value">{{ $order->tracking_number ?: '—' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Est. Delivery</div>
                                <div class="detail-value">{{ $order->estimated_delivery_date ? \Carbon\Carbon::parse($order->estimated_delivery_date)->format('d M Y') : '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Gift Order ───────────────────────────────────────────────── --}}
        @if($order->giftOrder)
        @php $gift = $order->giftOrder; @endphp
        <div class="wg-box mb-4" style="border-left:4px solid #f39c12;">
            <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
                <h5 class="mb-0">🎁 Gift Order</h5>
                @if($gift->gift_wrapping)
                <span style="background:#fff3e0;color:#e65100;border:1px solid #ffcc80;border-radius:20px;padding:3px 12px;font-size:12px;font-weight:700;">
                    🎀 Gift Wrapping Requested
                </span>
                @endif
                @if($order->requested_delivery_date)
                <span style="background:#f3e5f5;color:#6a1b9a;border:1px solid #ce93d8;border-radius:20px;padding:3px 12px;font-size:12px;font-weight:700;">
                    📅 Deliver by {{ \Carbon\Carbon::parse($order->requested_delivery_date)->format('d M Y') }}
                </span>
                @endif
            </div>

            <div class="row g-3">
                {{-- Sender --}}
                <div class="col-md-6">
                    <div style="background:#fff8e1;border:1px solid #ffe082;border-radius:8px;padding:14px 16px;">
                        <div class="detail-label mb-2" style="color:#f57f17;">📤 Sender</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="detail-label">Name</div>
                                <div class="detail-value">{{ $gift->sender_name ?: '—' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">{{ $gift->sender_phone ?: '—' }}</div>
                            </div>
                            @if($gift->sender_email)
                            <div class="col-12">
                                <div class="detail-label">Email</div>
                                <div class="detail-value">{{ $gift->sender_email }}</div>
                            </div>
                            @endif
                            @if($gift->sender_address)
                            <div class="col-8">
                                <div class="detail-label">Address</div>
                                <div class="detail-value">{{ $gift->sender_address }}</div>
                            </div>
                            @endif
                            @if($gift->sender_city)
                            <div class="col-4">
                                <div class="detail-label">City</div>
                                <div class="detail-value">{{ $gift->sender_city }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Receiver --}}
                <div class="col-md-6">
                    <div style="background:#f3e5f5;border:1px solid #ce93d8;border-radius:8px;padding:14px 16px;">
                        <div class="detail-label mb-2" style="color:#6a1b9a;">📥 Receiver</div>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="detail-label">Name</div>
                                <div class="detail-value">{{ $gift->receiver_name ?: '—' }}</div>
                            </div>
                            <div class="col-6">
                                <div class="detail-label">Phone</div>
                                <div class="detail-value">{{ $gift->receiver_phone ?: '—' }}</div>
                            </div>
                            @if($gift->receiver_address)
                            <div class="col-8">
                                <div class="detail-label">Delivery Address</div>
                                <div class="detail-value">{{ $gift->receiver_address }}</div>
                            </div>
                            @endif
                            @if($gift->receiver_city)
                            <div class="col-4">
                                <div class="detail-label">City</div>
                                <div class="detail-value">{{ $gift->receiver_city }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Gift Message --}}
                @if($gift->gift_message)
                <div class="col-12">
                    <div style="background:#f9fbe7;border:1px solid #dce775;border-radius:8px;padding:12px 16px;">
                        <div class="detail-label mb-1" style="color:#827717;">✉ Gift Message</div>
                        <div class="detail-value fst-italic" style="font-size:15px;color:#33691e;">
                            "{{ $gift->gift_message }}"
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Order Items ──────────────────────────────────────────────── --}}
        <div class="wg-box mb-4">
            <h5 class="mb-3">Order Items <span class="text-muted fs-6 fw-normal">({{ $orderItems->count() }} item{{ $orderItems->count() != 1 ? 's' : '' }})</span></h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="min-width:260px;">Product</th>
                            <th class="text-center">Variant</th>
                            <th class="text-center">Unit Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Subtotal</th>
                            <th class="text-center">Category</th>
                            <th class="text-center">Return</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderItems as $item)
                        <tr>
                            <td class="pname">
                                <div class="d-flex align-items-center gap-2">
                                    @if($item->product)
                                    <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                         alt="{{ $item->product->name }}"
                                         class="rounded"
                                         style="width:48px;height:48px;object-fit:cover;">
                                    @endif
                                    <div>
                                        @if($item->product)
                                        <a href="{{ route('shop.product.details', ['product_slug' => $item->product->slug]) }}"
                                           target="_blank" class="body-title-2 d-block">{{ $item->product->name }}</a>
                                        @else
                                            <span class="text-muted">Deleted product</span>
                                        @endif
                                        @if($item->variant)
                                            <small class="text-muted">SKU: {{ $item->variant->sku ?: '—' }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($item->variant_label)
                                    <span class="badge bg-light text-dark border">{{ $item->variant_label }}</span>
                                @elseif($item->variant)
                                    <span class="badge bg-light text-dark border">{{ $item->variant->display_label }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">Rs {{ number_format($item->price, 2) }}</td>
                            <td class="text-center fw-bold">{{ $item->quantity }}</td>
                            <td class="text-center fw-bold text-success">Rs {{ number_format($item->price * $item->quantity, 2) }}</td>
                            <td class="text-center">
                                {{ $item->product?->category?->name ?? '—' }}
                            </td>
                            <td class="text-center">
                                @if($item->rstatus)
                                    <span class="badge bg-warning">Returned</span>
                                @else
                                    <span class="text-muted small">No</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No items found.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="4" class="text-end fw-bold">Order Total</td>
                            <td class="text-center fw-bold text-success" style="font-size:15px;">Rs {{ number_format($order->total, 2) }}</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- ── Order Timeline ───────────────────────────────────────────── --}}
        @if($order->histories->count())
        <div class="wg-box mb-4">
            <h5 class="mb-3">Order Timeline</h5>
            <div class="timeline mt-3">
                @foreach($order->histories as $history)
                @php
                    $itemClass = in_array($history->status, ['delivered','paid']) ? 'done'
                        : (in_array($history->status, ['canceled']) ? 'canceled'
                        : ($history->status === 'returned' ? 'returned' : ''));
                @endphp
                <div class="timeline-item {{ $itemClass }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-{{ $statusColors[$history->status] ?? 'secondary' }} me-2">{{ ucfirst($history->status) }}</span>
                            @if($history->note)
                                <span class="text-muted small">{{ $history->note }}</span>
                            @endif
                        </div>
                        <small class="text-muted ms-3 text-nowrap">
                            {{ $history->created_at->format('d M Y, H:i') }}
                            @if($history->creator) &mdash; {{ $history->creator->name }} @endif
                        </small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Update Status ────────────────────────────────────────────── --}}
        <div class="wg-box">
            <h5 class="mb-3">Update Order Status</h5>

            {{-- Shared status picker --}}
            <div class="mb-4">
                <label class="form-label fw-600">New Status</label>
                <select id="order_status_picker" class="form-select" style="max-width:220px;">
                    @foreach(['ordered','confirmed','packed','shipped','delivered','canceled','returned'] as $s)
                    <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Form A: Simple update (non-shipped statuses) --}}
            <form id="form-simple-update" action="{{ route('admin.order.status.update') }}" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="order_status" id="simple_status_val" value="{{ $order->status }}">
                <div class="row g-3">
                    <div class="col-md-9">
                        <label class="form-label">Admin Note <small class="text-muted">(optional)</small></label>
                        <input type="text" name="admin_note" class="form-control" maxlength="500"
                               placeholder="Internal note about this status change">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="tf-button style-1 w-100">Update Status</button>
                    </div>
                </div>
            </form>

            {{-- Form B: Create Shipment (shown when "shipped" is selected) --}}
            <form id="form-create-shipment" action="{{ route('admin.shipments.store') }}" method="POST" style="display:none;">
                @csrf
                <input type="hidden" name="order_id" value="{{ $order->id }}">
                <input type="hidden" name="courier_service_id" id="ship_courier_id" value="{{ $couriers->first()?->id }}">

                @if($existingShipment)
                <div class="alert alert-warning mb-3 small">
                    ⚠ An active shipment already exists:
                    <a href="{{ route('admin.shipments.show', $existingShipment) }}" class="fw-bold">
                        {{ $existingShipment->tracking_number ?: '#'.$existingShipment->id }}
                    </a> — {{ $existingShipment->status_label }}.
                </div>
                @else
                <div class="alert alert-info mb-3 small">
                    Fill in the shipping details below. The order status will be set to <strong>Shipped</strong> automatically.
                </div>
                @endif

                {{-- Ship Via --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-5">
                        <label class="form-label fw-600">Ship Via <span class="text-danger">*</span></label>
                        <select id="ship_courier_select" class="form-select">
                            @foreach($couriers as $c)
                            <option value="{{ $c->id }}" data-code="{{ $c->code }}">
                                @if($c->code === 'internal') 🚐 Farmer's Basket Delivery
                                @elseif($c->code === 'leopards') 🐆 Leopards Courier
                                @elseif($c->code === 'tcs') 📦 TCS Couriers
                                @elseif($c->code === 'mnp') 🚛 M&P Express
                                @else {{ $c->name }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Internal: rider + schedule --}}
                <div id="ship-panel-internal" class="row g-3 mb-3" style="display:none;">
                    <div class="col-md-4">
                        <label class="form-label fw-600">Rider <span class="text-danger">*</span></label>
                        <select name="rider_id" class="form-select">
                            <option value="">— Select Rider —</option>
                            @foreach($riders as $rider)
                            <option value="{{ $rider->id }}">
                                {{ $rider->name }} ({{ $rider->vehicle_label }}){{ $rider->branch ? ' — '.$rider->branch->name : '' }}
                            </option>
                            @endforeach
                        </select>
                        @if($riders->isEmpty())
                            <div class="form-text text-warning">No active riders.
                                <a href="{{ route('admin.riders.index') }}" target="_blank">Add a rider →</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Vehicle <span class="text-danger">*</span></label>
                        <select name="vehicle_type" class="form-select">
                            <option value="">— Select —</option>
                            @foreach(App\Models\Rider::VEHICLES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Delivery Date <span class="text-danger">*</span></label>
                        <input type="date" name="estimated_delivery" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-600">Time Slot <span class="text-danger">*</span></label>
                        <select name="delivery_time_slot" class="form-select">
                            <option value="">— Select —</option>
                            @foreach(App\Models\Shipment::TIME_SLOTS as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- External: parcel details --}}
                <div id="ship-panel-external" class="row g-3 mb-3" style="display:none;">
                    <div class="col-md-3">
                        <label class="form-label">Weight (KG) <span class="text-danger">*</span></label>
                        <input type="number" name="weight" class="form-control" step="0.1" min="0.1" value="0.5">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Pieces <span class="text-danger">*</span></label>
                        <input type="number" name="pieces" class="form-control" min="1" value="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Declared Value (Rs) <span class="text-danger">*</span></label>
                        <input type="number" name="declared_value" class="form-control" min="0" value="{{ $order->total }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Est. Delivery Date</label>
                        <input type="date" name="estimated_delivery" class="form-control" min="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Special Instructions</label>
                        <input type="text" name="special_instructions" class="form-control"
                               value="Handle with care — Fresh Fruit" maxlength="500">
                    </div>
                </div>

                {{-- Recipient & Route (collapsible, pre-filled) --}}
                <div class="border rounded p-3 mb-3" style="background:#f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center mb-1" id="recipient-toggle-btn" style="cursor:pointer;">
                        <span class="fw-600 small text-muted text-uppercase" style="letter-spacing:.4px;">Recipient & Route</span>
                        <span id="recipient-toggle-icon" class="small text-primary">▼ Edit</span>
                    </div>
                    <div id="recipient-fields" style="display:none;">
                        <div class="row g-2 mt-1">
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Recipient Name *</label>
                                <input type="text" name="recipient_name" class="form-control form-control-sm"
                                       value="{{ $order->name }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Phone *</label>
                                <input type="text" name="recipient_phone" class="form-control form-control-sm"
                                       value="{{ $order->phone }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-1">Address *</label>
                                <input type="text" name="recipient_address" class="form-control form-control-sm"
                                       value="{{ trim($order->address . ($order->locality ? ', '.$order->locality : '')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Origin City *</label>
                                <input type="text" name="origin_city" class="form-control form-control-sm"
                                       value="Multan" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small mb-1">Destination City *</label>
                                <input type="text" name="destination_city" class="form-control form-control-sm"
                                       value="{{ $order->city }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label small mb-1">Notes</label>
                                <input type="text" name="notes" class="form-control form-control-sm" maxlength="500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="tf-button style-1" id="ship-submit-btn">🚐 Assign & Dispatch</button>
                    <button type="button" id="ship-cancel-btn" class="tf-button" style="background:#eee;color:#333;">Cancel</button>
                </div>
            </form>

        </div>

    </div>
</div>

@push('scripts')
<script>
$(function () {

    var initialStatus = '{{ $order->status }}';

    /* ── Switch between simple-update form and shipment-create form ── */
    function switchStatusMode(status) {
        var isShipped = (status === 'shipped');
        $('#form-simple-update').toggle(!isShipped);
        $('#simple_status_val').val(status);
        $('#form-create-shipment').toggle(isShipped);
    }

    /* ── Show the right panel based on chosen courier ── */
    function onShipCourierChange(courierId, courierCode) {
        $('#ship_courier_id').val(courierId);
        var isInternal = (courierCode === 'internal');
        $('#ship-panel-internal').toggle(isInternal);
        $('#ship-panel-external').toggle(!isInternal);
        $('#ship-submit-btn').text(isInternal ? '🚐 Assign & Dispatch' : '📦 Book Shipment');
    }

    /* Init courier panel on page load */
    var $courierSel = $('#ship_courier_select');
    if ($courierSel.length) {
        var $opt = $courierSel.find('option:selected');
        onShipCourierChange($opt.val(), $opt.data('code'));
    }

    /* Courier dropdown change */
    $('#ship_courier_select').on('change', function () {
        var $sel = $(this).find('option:selected');
        onShipCourierChange($sel.val(), $sel.data('code'));
    });

    /* Recipient toggle */
    $('#recipient-toggle-btn').on('click', function () {
        var $fields = $('#recipient-fields');
        var open = $fields.is(':visible');
        $fields.toggle(!open);
        $('#recipient-toggle-icon').text(open ? '▼ Edit' : '▲ Collapse');
    });

    /* Cancel button restores original status */
    $('#ship-cancel-btn').on('click', function () {
        $('#order_status_picker').val(initialStatus);
        switchStatusMode(initialStatus);
    });

    /* Status picker change */
    $('#order_status_picker').on('change', function () {
        switchStatusMode($(this).val());
    });

    /* Initialise on load */
    switchStatusMode(initialStatus);

});
</script>
@endpush
@endsection
