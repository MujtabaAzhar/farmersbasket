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
                    {{ $isPOS ? '🖥 POS' : '🌐 Website / E-Commerce' }}
                </span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button onclick="printScreenReceipt()" class="tf-button" style="font-size:13px;padding:6px 14px;background:#2ecc71;color:#fff;border:none;cursor:pointer;">
                    🖨 Print Receipt
                </button>
                <button onclick="printStickers()" class="tf-button" style="font-size:13px;padding:6px 14px;background:#e67e22;color:#fff;border:none;cursor:pointer;">
                    📦 Print Stickers
                </button>
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
                        @if($order->shipping > 0)
                        <tr>
                            <td class="detail-label ps-0">
                                Shipping
                                @if($order->courier_name)
                                    <span style="font-weight:400;color:#888;">({{ $order->courier_name }})</span>
                                @endif
                            </td>
                            <td class="text-end">Rs {{ number_format($order->shipping, 2) }}</td>
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
                            @php
                                $tx  = $order->transaction;
                                $ts  = $tx->status ?? 'pending';
                                $ext = $tx->payment_receipt ? strtolower(pathinfo($tx->payment_receipt, PATHINFO_EXTENSION)) : null;
                                $isPdf = $ext === 'pdf';
                            @endphp
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="detail-label">Payment Mode</div>
                                    <div class="detail-value text-capitalize">{{ str_replace('_', ' ', $tx->mode ?? '—') }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="detail-label">Transaction Status</div>
                                    <div class="detail-value">
                                        <span class="badge bg-{{ $ts === 'approved' ? 'success' : ($ts === 'declined' ? 'danger' : ($ts === 'refunded' ? 'secondary' : 'warning')) }}">{{ ucfirst($ts) }}</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Payment slip / receipt uploaded by customer --}}
                            @if($tx->payment_receipt)
                            <div class="mt-3">
                                <div class="detail-label mb-2">Payment Slip</div>
                                @if($isPdf)
                                    <a href="{{ asset('uploads/payment_receipts/' . $tx->payment_receipt) }}"
                                       target="_blank"
                                       class="tf-button style-1"
                                       style="font-size:13px;padding:6px 14px;display:inline-flex;align-items:center;gap:6px;">
                                        📄 View PDF Receipt
                                    </a>
                                @else
                                    {{-- Button to open slip in lightbox modal --}}
                                    <button type="button"
                                            onclick="document.getElementById('slipModal').style.display='flex'"
                                            class="tf-button style-1"
                                            style="font-size:13px;padding:6px 16px;display:inline-flex;align-items:center;gap:6px;">
                                        🧾 View Payment Slip
                                    </button>

                                    {{-- Lightbox Modal --}}
                                    <div id="slipModal"
                                         onclick="if(event.target===this)this.style.display='none'"
                                         style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.75);z-index:9999;align-items:center;justify-content:center;padding:20px;">
                                        <div style="position:relative;max-width:90vw;max-height:90vh;">
                                            <button onclick="document.getElementById('slipModal').style.display='none'"
                                                    style="position:absolute;top:-14px;right:-14px;background:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:18px;cursor:pointer;line-height:1;box-shadow:0 2px 8px rgba(0,0,0,.3);">
                                                ×
                                            </button>
                                            <img src="{{ asset('uploads/payment_receipts/' . $tx->payment_receipt) }}"
                                                 alt="Payment Slip"
                                                 style="max-width:90vw;max-height:88vh;border-radius:8px;display:block;box-shadow:0 4px 24px rgba(0,0,0,.5);">
                                            <a href="{{ asset('uploads/payment_receipts/' . $tx->payment_receipt) }}"
                                               download
                                               style="display:block;text-align:center;margin-top:10px;color:#fff;font-size:13px;">
                                                ⬇ Download
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @else
                                <div class="mt-2 text-muted small">No payment slip uploaded.</div>
                            @endif
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
                                    @if($item->product && $item->product->image)
                                    <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                         alt="{{ $item->product->name }}"
                                         class="rounded"
                                         style="width:48px;height:48px;object-fit:cover;"
                                         onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                    @elseif($item->product)
                                    <div class="rounded d-flex align-items-center justify-content-center bg-light"
                                         style="width:48px;height:48px;font-size:20px;">🛒</div>
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

            {{-- Form B: Shipped — courier + tracking number only --}}
            <form id="form-shipped" action="{{ route('admin.order.status.update') }}" method="POST" style="display:none;">
                @csrf @method('PUT')
                <input type="hidden" name="order_id"     value="{{ $order->id }}">
                <input type="hidden" name="order_status" value="shipped">

                <div class="alert alert-info mb-3 small">
                    Select a courier and enter the tracking number. The customer will automatically receive a WhatsApp message with the full tracking link.
                </div>

                <div class="row g-3">
                    <div class="col-md-5">
                        <label class="form-label fw-600">Courier <span class="text-danger">*</span></label>
                        <select name="courier_name" id="shipped_courier" class="form-select" required>
                            <option value="">— Select Courier —</option>
                            @foreach($couriers as $c)
                            <option value="{{ $c->name }}"
                                    data-tracking="{{ $c->tracking_url_template }}"
                                    {{ $order->courier_name === $c->name ? 'selected' : '' }}>
                                {{ $c->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-600">Tracking Number <span class="text-danger">*</span></label>
                        <input type="text" name="tracking_number" id="shipped_tracking"
                               class="form-control" required maxlength="100"
                               placeholder="e.g. LRS1234567890"
                               value="{{ $order->tracking_number }}">
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Admin Note <small class="text-muted">(optional)</small></label>
                        <input type="text" name="admin_note" class="form-control" maxlength="500"
                               placeholder="e.g. Dispatched from warehouse">
                    </div>

                    {{-- Live tracking link preview --}}
                    <div class="col-12" id="tracking-preview" style="display:none;">
                        <div class="alert alert-success py-2 mb-0 small d-flex align-items-center gap-2">
                            🔗 Tracking link:
                            <a id="tracking-preview-link" href="#" target="_blank" style="word-break:break-all;"></a>
                        </div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="tf-button style-1">🚚 Mark as Shipped &amp; Notify Customer</button>
                        <button type="button" id="shipped-cancel-btn" class="tf-button" style="background:#eee;color:#333;">Cancel</button>
                    </div>
                </div>
            </form>

        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Build print data from server ─────────────────────────────────────────────
@php
    $gift        = $order->giftOrder;
    $cashierName = $isPOS ? ($order->cashier?->name ?? auth()->user()->name) : auth()->user()->name;

    if ($isPOS && $order->posPayment) {
        $payMethod    = strtoupper(str_replace('_', ' ', $order->posPayment->method));
        $payDetail    = $order->posPayment->online_platform ? ucfirst($order->posPayment->online_platform) : null;
        $cashReceived = $order->posPayment->cash_received ?? null;
        $changeGiven  = $order->posPayment->change_given  ?? null;
    } elseif (!$isPOS && $order->transaction) {
        $payMethod    = strtoupper($order->transaction->mode ?? 'N/A');
        $payDetail    = null;
        $cashReceived = null;
        $changeGiven  = null;
    } else {
        $payMethod    = 'N/A';
        $payDetail    = null;
        $cashReceived = null;
        $changeGiven  = null;
    }

    $stickerPayload = [
        'orderNumber' => $order->order_number,
        'createdAt'   => $order->created_at->format('n/j/y, g:i A'),
        'cashier'     => $cashierName,
        'isGift'      => (bool) $gift,
        'to' => $gift
            ? ['name' => $gift->receiver_name, 'phone' => $gift->receiver_phone,
               'address' => $gift->receiver_address ?? '', 'city' => $gift->receiver_city ?? '']
            : ['name' => $order->name ?? '', 'phone' => $order->phone ?? '',
               'address' => $order->address ?? '', 'city' => $order->city ?? ''],
        'from' => $gift
            ? ['name' => $gift->sender_name ?: "FARMER'S BASKET",
               'phone' => $gift->sender_phone ?: '03-111-222-384',
               'address' => $gift->sender_address ?? '', 'city' => $gift->sender_city ?? '']
            : ['name'    => ($order->branch?->name ?: "FARMER'S BASKET"),
               'address' => ($order->branch?->address ?: ''),
               'phone'   => '03-111-222-384',
               'city'    => ($order->branch?->city ?: 'Lahore')],
        'message' => $gift?->gift_message ?? '',
        'items'   => $orderItems->map(fn($i) => [
            'name'    => $i->product?->name ?? ('Product #' . $i->product_id),
            'brand'   => $i->product?->brand?->name ?? '',
            'variant' => $i->variant_label ?? '',
            'qty'     => $i->quantity,
        ])->values()->all(),
    ];

    $receiptPayload = [
        'orderNumber'   => $order->order_number,
        'createdAt'     => $order->created_at->format('d/m/Y H:i'),
        'cashier'       => $cashierName,
        'branch'        => $order->branch?->name ?: "FARMER'S BASKET",
        'paymentMethod' => $payMethod,
        'paymentDetail' => $payDetail,
        'cashReceived'  => $cashReceived,
        'changeGiven'   => $changeGiven,
        'to' => $gift
            ? ['name' => $gift->receiver_name, 'phone' => $gift->receiver_phone,
               'address' => $gift->receiver_address ?? '', 'city' => $gift->receiver_city ?? '']
            : ['name' => $order->name ?? '', 'phone' => $order->phone ?? '',
               'address' => $order->address ?? '', 'city' => $order->city ?? ''],
        'from' => $gift
            ? ['name'  => $gift->sender_name  ?: "FARMER'S BASKET",
               'phone' => $gift->sender_phone ?: '03-111-222-384',
               'city'  => $gift->sender_city  ?: '']
            : ['name'  => $order->branch?->name ?: "FARMER'S BASKET",
               'phone' => '03-111-222-384',
               'city'  => $order->branch?->city ?: 'Lahore'],
        'items'       => $orderItems->map(fn($i) => [
            'name'    => $i->product?->name ?? ('Product #' . $i->product_id),
            'brand'   => $i->product?->brand?->name ?? '',
            'variant' => $i->variant_label ?? '',
            'qty'     => $i->quantity,
            'price'   => $i->price,
            'total'   => $i->price * $i->quantity,
        ])->values()->all(),
        'totalQty'    => $orderItems->sum('quantity'),
        'subtotal'    => $order->subtotal,
        'tax'         => $order->tax      ?? 0,
        'shipping'    => $order->shipping ?? 0,
        'discount'    => $order->discount ?? 0,
        'total'       => $order->total,
        'courierName' => $order->courier_name ?? '',
        'orderNote'   => $order->order_note  ?? '',
        'logoUrl'     => asset('images/logo/logo.png'),
    ];
@endphp
var stickerData = @json($stickerPayload);
var receiptData = @json($receiptPayload);

// ── Helpers ───────────────────────────────────────────────────────────────────
function escH(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

// ── Sticker printer ───────────────────────────────────────────────────────────
function buildSticker(item, stickerIdx, totalStickers) {
    var d = stickerData;
    var s = '<div class="sticker">';
    s += '<div class="s-inv-lg">Invoice No: ' + escH(d.orderNumber) + ' / ' + totalStickers + '</div>';
    s += '<div class="s-user-row">'
       + '<span>USER: ' + escH(d.cashier) + '</span>'
       + (item.brand ? '<span>Brand: <strong>' + escH(item.brand) + '</strong></span>' : '')
       + '</div>';
    var addrClass = (d.to.address && d.to.address.length > 45) ? 's-detail-sm' : 's-detail';
    s += '<div class="s-combined-box">';
    s += '<div class="s-box-title">TO</div>';
    s += '<div class="s-to-name">' + escH(d.to.name) + '</div>';
    if (d.to.address) s += '<div class="' + addrClass + '">Delivery Address: ' + escH(d.to.address) + '</div>';
    s += '<div class="s-detail">Contact: ' + escH(d.to.phone) + '</div>';
    if (d.to.city) s += '<div class="s-city-bar">City: ' + escH(d.to.city).toUpperCase() + '</div>';
    s += '<div class="s-inner-divider"></div>';
    s += '<div class="s-box-title">FROM</div>';
    s += '<div class="s-from-name">' + escH(d.from.name) + '</div>';
    s += '<div class="s-detail">Contact: ' + escH(d.from.phone) + '</div>';
    if (d.from.city) s += '<div class="s-city-bar">City: ' + escH(d.from.city) + '</div>';
    s += '</div>';
    s += '<div class="s-helpline">In case of any query please contact our customer Helpline 03-111-222-384</div>';
    s += '</div>';
    return s;
}

function printStickers() {
    var css = [
        '* { margin:0; padding:0; box-sizing:border-box; }',
        'body { font-family:Arial,Helvetica,sans-serif; color:#000; background:#fff; }',
        '@page { size:4in 4in; margin:0; }',
        '.sticker { width:4in; height:4in; padding:10px 14px 8px; display:flex; flex-direction:column; gap:5px; page-break-after:always; overflow:hidden; }',
        '.sticker:last-child { page-break-after:avoid; }',
        '.s-inv-lg        { font-size:14pt; font-weight:bold; margin-top:1px; text-align:center; }',
        '.s-helpline      { font-size:9.5pt; font-weight:bold; color:#000; text-align:center; margin-top:1px; padding-top:4px; }',
        '.s-user-row      { display:flex; justify-content:space-between; font-size:9.5pt; font-weight:600; letter-spacing:.3px; }',
        '.s-combined-box  { border:2px solid #000; padding:6px 10px 5px; }',
        '.s-inner-divider { border-top:1px dashed #000; margin:6px 0; }',
        '.s-box-title     { text-align:center; font-weight:bold; font-size:13pt; letter-spacing:2px; margin-bottom:3px; }',
        '.s-to-name       { font-size:13pt; font-weight:bold; line-height:1.15; }',
        '.s-from-name     { font-size:13pt; font-weight:700; }',
        '.s-detail        { font-size:13pt; margin:2px 0; }',
        '.s-detail-sm     { font-size:9pt; margin:2px 0; }',
        '.s-city-bar      { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:13pt; padding:3px 0; margin-top:5px; letter-spacing:1px; }',
    ].join('\n');

    var totalStickers = stickerData.items.reduce(function(sum, i){ return sum + i.qty; }, 0);
    var html = '', idx = 0;
    stickerData.items.forEach(function(item) {
        for (var i = 0; i < item.qty; i++) { html += buildSticker(item, idx, totalStickers); idx++; }
    });
    if (!html) { alert('No items to print stickers for.'); return; }

    var win = window.open('', '_blank', 'width=600,height=600,scrollbars=yes,resizable=yes');
    win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Stickers &mdash; ' + escH(stickerData.orderNumber) + '</title><style>' + css + '</style></head><body>' + html + '</body></html>');
    win.document.close(); win.focus();
    setTimeout(function(){ win.print(); }, 400);
}

// ── Receipt printer ───────────────────────────────────────────────────────────
function printScreenReceipt() {
    var d = receiptData;
    function fmt(n){ return 'Rs ' + parseFloat(n||0).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2}); }

    function buildSlip(copyLabel) {
        var addrClass = (d.to.address && d.to.address.length > 40) ? 'r-sm' : 'r-det';
        var header = '<div class="r-hdr">'
            + '<div class="r-hdr-left"><img src="' + escH(d.logoUrl) + '" class="r-logo" alt="Logo">'
            + '<div class="r-hdr-meta">'
            +   '<div class="r-meta-line">Date: ' + escH(d.createdAt) + ' &nbsp;|&nbsp; USER: ' + escH(d.cashier) + ' &nbsp;|&nbsp; <strong>' + escH(copyLabel) + '</strong></div>'
            +   '<div class="r-meta-line">Payment Method : <strong>' + escH(d.paymentMethod) + (d.paymentDetail ? ' (' + escH(d.paymentDetail) + ')' : '') + '</strong></div>'
            + '</div></div>'
            + '<div class="r-hdr-right">'
            +   '<div class="r-banner">' + escH(d.courierName || 'COUNTER SALE') + '</div>'
            +   '<div class="r-inv-block">Invoice No:<br><strong>' + escH(d.orderNumber) + ' / ' + d.totalQty + '</strong></div>'
            + '</div></div>';

        var tofrom = '<div class="r-tofrom">'
            + '<div class="r-to-col"><div class="r-col-hdr">TO</div>'
            +   '<div class="r-name">' + escH(d.to.name) + '</div>'
            +   (d.to.address ? '<div class="' + addrClass + '">Delivery Address: ' + escH(d.to.address) + '</div>' : '')
            +   '<div class="r-det">Contact: ' + escH(d.to.phone) + '</div>'
            +   (d.to.city ? '<div class="r-city">City: ' + escH(d.to.city).toUpperCase() + '</div>' : '')
            + '</div>'
            + '<div class="r-from-col"><div class="r-col-hdr">FROM</div>'
            +   '<div class="r-name">' + escH(d.from.name) + '</div>'
            +   '<div class="r-det">Address:</div>'
            +   '<div class="r-det">Contact: ' + escH(d.from.phone) + '</div>'
            +   (d.from.city ? '<div class="r-city">City: ' + escH(d.from.city) + '</div>' : '')
            + '</div></div>';

        var itemLines = '';
        d.items.forEach(function(item) {
            var label = escH(item.name) + (item.variant ? ' (' + escH(item.variant) + ')' : '');
            var brand = item.brand ? ' <span class="r-brand">' + escH(item.brand) + '</span>' : '';
            itemLines += '<tr><td class="r-dt-lbl">' + label + brand + '</td>'
                + '<td class="r-dt-mid">' + item.qty + ' &times; ' + fmt(item.price) + '</td>'
                + '<td class="r-dt-val">' + fmt(item.total) + '</td></tr>';
        });
        var delivRow = d.shipping > 0
            ? '<tr><td class="r-dt-lbl">Delivery By: ' + (d.courierName ? '<span class="r-courier-banner">'+escH(d.courierName)+'</span>' : '') + '</td>'
              + '<td class="r-dt-mid">' + d.totalQty + ' &times; ' + fmt(d.shipping / d.totalQty) + '</td>'
              + '<td class="r-dt-val">' + fmt(d.shipping) + '</td></tr>' : '';
        var discRow = d.discount > 0
            ? '<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">- ' + fmt(d.discount) + '</td></tr>'
            : '<tr><td class="r-dt-lbl">Disc Amt (%):</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(0) + '</td></tr>';

        var details = '<div class="r-details"><table class="r-dt-table"><tbody>'
            + '<tr><td class="r-dt-branch" colspan="3">' + escH(d.branch) + '</td></tr>'
            + '<tr><td class="r-dt-lbl">Total Item(s): ' + d.totalQty + '</td><td class="r-dt-mid"></td><td class="r-dt-val"></td></tr>'
            + itemLines
            + '<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
            + '<tr><td class="r-dt-lbl">Sub Total:</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(d.subtotal) + '</td></tr>'
            + discRow + delivRow
            + '<tr class="r-divrow"><td colspan="3"><div class="r-thin-line"></div></td></tr>'
            + '<tr><td class="r-dt-grand">Grand Total:</td><td class="r-dt-mid"></td><td class="r-dt-grand r-dt-val">' + fmt(d.total) + '</td></tr>'
            + '<tr><td class="r-dt-lbl">Paid Amount:</td><td class="r-dt-mid"></td><td class="r-dt-val">' + fmt(d.total) + '</td></tr>'
            + '</tbody></table></div>';

        var footer = '<div class="r-footer">For Inquiries &amp; suggestions Please Contact: Help line: <strong>03-111-222-384</strong><br>'
            + 'Thanks For Visiting At Farmer\'s Basket &mdash; For any Query and Complaint Call Us at 03-111-222-384</div>';

        return '<div class="slip">' + header + tofrom + details + footer + '</div>';
    }

    var css = [
        '* { margin:0; padding:0; box-sizing:border-box; }',
        'body { font-family:Arial,Helvetica,sans-serif; font-size:9.5pt; color:#000; background:#fff; width:210mm; }',
        '@page { size:A4 portrait; margin:0; }',
        '.slip     { width:210mm; height:148.5mm; padding:4mm 6mm 3mm; display:flex; flex-direction:column; overflow:hidden; }',
        '.cut-line { width:210mm; height:4mm; display:flex; align-items:center; justify-content:center; font-size:8pt; color:#aaa; letter-spacing:2px; border-top:1px dashed #bbb; border-bottom:1px dashed #bbb; }',
        '.r-hdr       { display:flex; align-items:stretch; gap:4mm; border-bottom:1.5px solid #000; padding-bottom:2mm; margin-bottom:2mm; }',
        '.r-hdr-left  { display:flex; align-items:center; gap:3mm; flex:1; }',
        '.r-logo      { height:34px; width:auto; flex-shrink:0; }',
        '.r-hdr-meta  { display:flex; flex-direction:column; gap:2px; }',
        '.r-meta-line { font-size:8.5pt; color:#222; }',
        '.r-hdr-right { display:flex; flex-direction:column; align-items:flex-end; justify-content:center; min-width:65mm; }',
        '.r-banner    { background:#000; color:#fff; font-size:12pt; font-weight:bold; letter-spacing:1px; padding:4px 8px; text-align:center; width:100%; }',
        '.r-inv-block { font-size:9.5pt; text-align:right; margin-top:2px; line-height:1.4; }',
        '.r-tofrom    { display:flex; border:1.5px solid #000; margin-bottom:2mm; }',
        '.r-to-col    { flex:1; padding:2mm 3mm; border-right:1.5px solid #000; }',
        '.r-from-col  { flex:1; padding:2mm 3mm; }',
        '.r-col-hdr   { text-align:center; font-weight:bold; font-size:11pt; letter-spacing:1px; border-bottom:1px solid #000; margin-bottom:1.5mm; padding-bottom:2px; }',
        '.r-name      { font-size:11pt; font-weight:bold; line-height:1.3; }',
        '.r-det       { font-size:9pt; margin:2px 0; }',
        '.r-sm        { font-size:7.5pt; margin:2px 0; }',
        '.r-city      { background:#000; color:#fff; text-align:center; font-weight:bold; font-size:10pt; padding:3px 0; margin-top:2mm; letter-spacing:1px; }',
        '.r-details       { flex:1; border:1.5px solid #000; padding:1.5mm 2.5mm; margin-bottom:1.5mm; }',
        '.r-dt-table      { width:100%; border-collapse:collapse; }',
        '.r-dt-table td   { font-size:9pt; padding:2px 3px; vertical-align:middle; }',
        '.r-dt-branch     { font-weight:bold; font-size:10pt; padding-bottom:1.5mm !important; }',
        '.r-dt-lbl        { width:55%; }',
        '.r-dt-mid        { width:25%; text-align:center; color:#333; }',
        '.r-dt-val        { width:20%; text-align:right; font-weight:600; }',
        '.r-dt-grand      { font-weight:bold; font-size:10pt; }',
        '.r-brand         { background:#000; color:#fff; font-weight:bold; font-size:8pt; margin-left:3px; padding:1px 6px; }',
        '.r-thin-line     { border-top:1px solid #000; margin:2px 0; }',
        '.r-courier-banner{ background:#000; color:#fff; font-weight:bold; padding:1px 6px; font-size:8pt; letter-spacing:.5px; }',
        '.r-footer { text-align:center; font-size:8pt; border-top:1px solid #000; padding-top:1.5mm; line-height:1.6; margin-top:auto; }',
    ].join('\n');

    var slip1 = buildSlip('Customer Slip');
    var cut   = '<div class="cut-line">&#9988; &mdash;&mdash;&mdash;&mdash;&mdash; cut here &mdash;&mdash;&mdash;&mdash;&mdash; &#9988;</div>';
    var slip2 = buildSlip('Counter Copy');

    var win = window.open('', '_blank', 'width:800,height=900,scrollbars=yes,resizable=yes');
    win.document.write('<!DOCTYPE html><html><head><meta charset="utf-8"><title>Receipt &mdash; ' + escH(d.orderNumber) + '</title><style>' + css + '</style></head><body>' + slip1 + cut + slip2 + '</body></html>');
    win.document.close(); win.focus();
    setTimeout(function(){ win.print(); }, 400);
}
</script>
@endpush

@push('scripts')
<script>
$(function () {

    var initialStatus = '{{ $order->status }}';

    /* ── Switch between simple-update and shipped form ── */
    function switchStatusMode(status) {
        var isShipped = (status === 'shipped');
        $('#form-simple-update').toggle(!isShipped);
        $('#simple_status_val').val(status);
        $('#form-shipped').toggle(isShipped);
    }

    /* ── Live tracking link preview ── */
    function updateTrackingPreview() {
        var template = $('#shipped_courier').find('option:selected').data('tracking') || '';
        var number   = $('#shipped_tracking').val().trim();
        var preview  = $('#tracking-preview');
        var link     = $('#tracking-preview-link');

        if (template && number) {
            var url = template.replace('{tracking_number}', encodeURIComponent(number));
            link.attr('href', url).text(url);
            preview.show();
        } else {
            preview.hide();
        }
    }

    $('#shipped_courier, #shipped_tracking').on('change input', updateTrackingPreview);

    /* Cancel button restores original status */
    $('#shipped-cancel-btn').on('click', function () {
        $('#order_status_picker').val(initialStatus);
        switchStatusMode(initialStatus);
    });

    /* Status picker change */
    $('#order_status_picker').on('change', function () {
        switchStatusMode($(this).val());
    });

    /* Initialise on load */
    switchStatusMode(initialStatus);
    updateTrackingPreview();

});
</script>
@endpush
@endsection
