@extends('layouts.app')
@section('content')

@php
    if ($shipment) {
        $order     = $shipment->order;
        $courier   = $shipment->courier;
        $trackings = $shipment->trackings;

        $orderStatus   = $order->status;
        $shipStatus    = $shipment->status;
        $statusHistory = $trackings->pluck('status')->toArray();

        $journey = [
            ['key' => 'order_placed',     'label' => 'Order Placed',     'desc' => 'Your order has been received.',                        'done' => true],
            ['key' => 'confirmed',        'label' => 'Order Confirmed',   'desc' => 'Your order is confirmed and being prepared.',          'done' => in_array($orderStatus, ['confirmed','packed','shipped','delivered'])],
            ['key' => 'packed',           'label' => 'Packed',            'desc' => 'Your order has been packed and is ready for dispatch.','done' => in_array($orderStatus, ['packed','shipped','delivered'])],
            ['key' => 'picked_up',        'label' => 'Picked Up',         'desc' => $shipment->isInternal() ? 'Our rider has picked up your order.' : 'Parcel picked up by courier.',
                                                                                    'done' => in_array('picked_up', $statusHistory) || in_array($shipStatus, ['picked_up','in_transit','out_for_delivery','delivered'])],
            ['key' => 'in_transit',       'label' => 'In Transit',        'desc' => 'Your order is on its way to you.',                    'done' => in_array('in_transit', $statusHistory) || in_array($shipStatus, ['in_transit','out_for_delivery','delivered'])],
            ['key' => 'out_for_delivery', 'label' => 'Out for Delivery',  'desc' => 'Your order is out for delivery — arriving soon!',     'done' => in_array('out_for_delivery', $statusHistory) || in_array($shipStatus, ['out_for_delivery','delivered'])],
            ['key' => 'delivered',        'label' => 'Delivered',         'desc' => 'Your order has been delivered successfully.',          'done' => $shipStatus === 'delivered'],
        ];

        $isFailed   = in_array($shipStatus, ['failed','returned','canceled']);
        $doneCount  = collect($journey)->where('done', true)->count();
        $totalSteps = count($journey);

    } elseif ($order) {
        // No shipment yet — show order-level status only
        $orderStatus = $order->status;

        $journey = [
            ['key' => 'order_placed', 'label' => 'Order Placed',   'desc' => 'Your order has been received.',                         'done' => true],
            ['key' => 'confirmed',    'label' => 'Confirmed',       'desc' => 'Your order is confirmed and being prepared.',           'done' => in_array($orderStatus, ['confirmed','packed','shipped','delivered'])],
            ['key' => 'packed',       'label' => 'Packed',          'desc' => 'Your order has been packed and is ready for dispatch.', 'done' => in_array($orderStatus, ['packed','shipped','delivered'])],
            ['key' => 'dispatched',   'label' => 'Dispatched',      'desc' => 'Your order has been handed to our delivery team.',      'done' => in_array($orderStatus, ['shipped','delivered'])],
            ['key' => 'delivered',    'label' => 'Delivered',       'desc' => 'Your order has been delivered successfully.',           'done' => $orderStatus === 'delivered'],
        ];

        $isFailed   = in_array($orderStatus, ['canceled','returned']);
        $doneCount  = collect($journey)->where('done', true)->count();
        $totalSteps = count($journey);
        $shipment   = null;
    }
@endphp

{{-- Hero search --}}
<section class="breadcrumb-section position-relative fix bg-cover"
    style="background-image: url({{ asset('assets/img/hero/breadcrumb-banner.jpg') }});">
    <div class="container">
        <div class="breadcrumb-content">
            <h3 class="white-clr fs-1 fw-semibold text-center heading-font mb-2">Track Your Order</h3>
            <p class="fs-16 text-white text-center mb-4">Enter your tracking number or order ID below</p>
            <form action="{{ route('home.order.tracking') }}" method="GET"
                  class="search-adjust1 bg-white rounded-pill d-flex align-items-center max-w-750 mx-auto p-2 mb-md-5 mb-4">
                <input class="fs-14 w-100 py-2 px-4 border-0 bg-transparent"
                       type="text" name="tracking"
                       value="{{ request('tracking') }}"
                       placeholder="Tracking number (e.g. FB-2026-0001) or Order ID…"
                       autocomplete="off">
                <button type="submit" class="theme-btn py-2 px-4 fw-500 rounded-pill text-white">
                    <i class="fa-solid fa-magnifying-glass me-1"></i> Track
                </button>
            </form>
        </div>
    </div>
    <img src="{{ asset('assets/img/home-1/home-shape-start.png') }}" alt="" class="bread-shape-start position-absolute">
    <img src="{{ asset('assets/img/home-1/home-shape-end.png') }}" alt="" class="bread-shape-end position-absolute d-sm-block d-none">
</section>

<section class="section-padding fix">
    <div class="container">

        @if($error)
        <div class="alert alert-warning text-center" style="max-width:600px;margin:0 auto 24px;">
            {{ $error }}
        </div>
        @endif

        @if($shipment)

        {{-- Order header --}}
        <div class="shadow-cus p-lg-5 p-sm-4 p-3 mb-4 rounded-3">
            <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <h4 class="text-black mb-1 lh-1">{{ $order->order_number }}</h4>
                    <span class="fs-14 text-muted">Placed {{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="text-end">
                    <span class="badge bg-{{ $shipment->status_color }}" style="font-size:13px;padding:6px 14px;">
                        {{ $shipment->status_label }}
                    </span>
                    <div class="small text-muted mt-1">
                        @if($shipment->isInternal())
                            🚐 Farmer's Basket Delivery
                        @else
                            {{ $courier?->name }}
                        @endif
                    </div>
                </div>
            </div>

            {{-- Key info row --}}
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="bg-gray-10 rounded p-3">
                        <div class="fs-13 text-muted mb-1">Tracking Number</div>
                        <div class="fw-bold fs-16">{{ $shipment->tracking_number ?: '—' }}</div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="bg-gray-10 rounded p-3">
                        <div class="fs-13 text-muted mb-1">
                            @if($shipment->isInternal() && $shipment->delivery_time_slot)
                                Delivery Slot
                            @elseif($shipment->actual_delivery)
                                Delivered On
                            @else
                                Est. Delivery
                            @endif
                        </div>
                        <div class="fw-bold fs-16">
                            @if($shipment->actual_delivery)
                                <span class="text-success">{{ $shipment->actual_delivery->format('d M Y') }}</span>
                            @elseif($shipment->estimated_delivery)
                                {{ $shipment->estimated_delivery->format('d M Y') }}
                                @if($shipment->isInternal() && $shipment->delivery_time_slot)
                                    <div class="fw-normal fs-14 text-muted">{{ $shipment->time_slot_label }}</div>
                                @endif
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="bg-gray-10 rounded p-3">
                        <div class="fs-13 text-muted mb-1">Destination</div>
                        <div class="fw-bold fs-16">{{ $shipment->destination_city }}</div>
                    </div>
                </div>
            </div>

            @if(!$isFailed)
            {{-- Progress bar --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-600 fs-14">Delivery Progress</span>
                    <span class="fw-600 fs-14 theme-clr">{{ $doneCount }} / {{ $totalSteps }} steps</span>
                </div>
                <div class="progress" style="height:8px;border-radius:6px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ round(($doneCount/$totalSteps)*100) }}%;border-radius:6px;background:var(--theme-color,#2c9c3f);">
                    </div>
                </div>
            </div>

            {{-- Journey step-by-step --}}
            <div class="journey-steps d-flex flex-wrap gap-2 mt-4">
                @foreach($journey as $i => $step)
                <div class="journey-step flex-fill text-center" style="min-width:80px;">
                    <div class="step-circle mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold"
                         style="width:36px;height:36px;font-size:15px;
                                background:{{ $step['done'] ? 'var(--theme-color,#2c9c3f)' : '#e9ecef' }};
                                color:{{ $step['done'] ? '#fff' : '#aaa' }};">
                        @if($step['done']) ✓ @else {{ $i + 1 }} @endif
                    </div>
                    @if($i < count($journey)-1)
                    <div class="step-line d-none d-md-block" style="display:none;"></div>
                    @endif
                    <div class="fs-12 mt-1 {{ $step['done'] ? 'fw-600 text-black' : 'text-muted' }}"
                         style="line-height:1.3;">{{ $step['label'] }}</div>
                </div>
                @endforeach
            </div>
            @else
            {{-- Failed/Returned --}}
            <div class="alert alert-{{ $shipStatus === 'delivered' ? 'success' : 'danger' }} mt-3 mb-0">
                @if($shipStatus === 'failed') ⚠ Delivery attempted but failed. We will try again or contact you.
                @elseif($shipStatus === 'returned') ↩ Shipment returned to sender.
                @else Shipment canceled.
                @endif
            </div>
            @endif
        </div>

        <div class="row g-4 mb-4">

            {{-- Tracking timeline --}}
            <div class="col-md-7">
                <div class="shadow-cus p-lg-4 p-3 rounded-3">
                    <h5 class="text-black mb-4">Order Timeline</h5>
                    <ul class="preparetion-traking">
                        @foreach($journey as $step)
                        @if($step['done'])
                        <li class="d-flex position-relative gap-3 active pb-3">
                            <i class="fa-solid fa-circle fs-14 theme-clr" style="margin-top:4px;flex-shrink:0;"></i>
                            <div>
                                <h6 class="fw-600 mb-1">{{ $step['label'] }}</h6>
                                <p class="fs-14 text-muted mb-0">{{ $step['desc'] }}</p>
                                {{-- Try to match an actual tracking event time --}}
                                @php
                                    $event = $trackings->firstWhere('status', $step['key']);
                                    if (!$event && $step['key'] === 'order_placed') $event = null;
                                @endphp
                                @if($event)
                                    <p class="fs-13 text-black mt-1 mb-0">
                                        {{ \Carbon\Carbon::parse($event->event_time)->format('d M Y, h:i A') }}
                                        @if($event->location) &nbsp;·&nbsp; <i class="fa-solid fa-location-dot"></i> {{ $event->location }} @endif
                                    </p>
                                @elseif($step['key'] === 'order_placed')
                                    <p class="fs-13 text-black mt-1 mb-0">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                                @endif
                            </div>
                        </li>
                        @else
                        <li class="d-flex position-relative gap-3 pb-3" style="opacity:.45;">
                            <i class="fa-regular fa-circle fs-14 text-muted" style="margin-top:4px;flex-shrink:0;"></i>
                            <div>
                                <h6 class="fw-500 mb-1 text-muted">{{ $step['label'] }}</h6>
                                <p class="fs-14 text-muted mb-0">{{ $step['desc'] }}</p>
                            </div>
                        </li>
                        @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Order summary --}}
            <div class="col-md-5">
                <div class="shadow-cus p-lg-4 p-3 rounded-3">
                    <h5 class="text-black mb-3">Order Summary</h5>
                    <ul class="d-flex flex-column gap-2 mb-3">
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Order ID</span>
                            <span class="fw-600">{{ $order->order_number }}</span>
                        </li>
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Payment</span>
                            <span class="fw-600 {{ $order->payment_status === 'paid' ? 'text-success' : 'text-warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </li>
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Shipping Via</span>
                            <span class="fw-600">
                                @if($shipment->isInternal()) Farmer's Basket
                                @else {{ $courier?->name }}
                                @endif
                            </span>
                        </li>
                        @if($shipment->estimated_delivery && !$shipment->actual_delivery)
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Est. Delivery</span>
                            <span class="fw-600 theme-clr">{{ $shipment->estimated_delivery->format('d M Y') }}</span>
                        </li>
                        @endif
                    </ul>

                    @if($order->orderItems->count())
                    <div class="border-top pt-3">
                        <h6 class="mb-2">Items</h6>
                        @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @if($item->product?->image)
                            <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                 class="rounded" style="width:40px;height:40px;object-fit:cover;"
                                 onerror="this.style.display='none'">
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-500 fs-14">{{ $item->product?->name ?? '—' }}</div>
                                @if($item->variant_label)
                                    <div class="fs-12 text-muted">{{ $item->variant_label }}</div>
                                @endif
                            </div>
                            <div class="text-end fs-14">
                                <div class="text-muted">× {{ $item->quantity }}</div>
                                <div class="fw-600">Rs {{ number_format($item->price * $item->quantity) }}</div>
                            </div>
                        </div>
                        @endforeach
                        <div class="border-top pt-2 d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span>Rs {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @elseif($order)
        {{-- Order found but no shipment yet — show order status only --}}
        <div class="shadow-cus p-lg-5 p-sm-4 p-3 mb-4 rounded-3">
            <div class="d-flex justify-content-between flex-wrap gap-3 mb-4">
                <div>
                    <h4 class="text-black mb-1 lh-1">{{ $order->order_number }}</h4>
                    <span class="fs-14 text-muted">Placed {{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="text-end">
                    @php
                        $sBadge = ['ordered'=>'warning','confirmed'=>'info','packed'=>'secondary','shipped'=>'primary','delivered'=>'success','canceled'=>'danger','returned'=>'dark'];
                    @endphp
                    <span class="badge bg-{{ $sBadge[$order->status] ?? 'secondary' }}" style="font-size:13px;padding:6px 14px;">
                        {{ ucfirst($order->status) }}
                    </span>
                    @if(!$isFailed && !in_array($order->status, ['shipped','delivered']))
                    <div class="small text-muted mt-1">Shipment details coming soon</div>
                    @endif
                </div>
            </div>

            @if($isFailed)
            <div class="alert alert-danger mb-0">
                @if($order->status === 'canceled') Shipment canceled.
                @else ↩ Order returned.
                @endif
            </div>
            @else
            {{-- Progress bar --}}
            <div class="mb-3">
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-600 fs-14">Order Progress</span>
                    <span class="fw-600 fs-14 theme-clr">{{ $doneCount }} / {{ $totalSteps }} steps</span>
                </div>
                <div class="progress" style="height:8px;border-radius:6px;">
                    <div class="progress-bar" role="progressbar"
                         style="width:{{ round(($doneCount/$totalSteps)*100) }}%;border-radius:6px;background:var(--theme-color,#2c9c3f);">
                    </div>
                </div>
            </div>

            {{-- Step circles --}}
            <div class="journey-steps d-flex flex-wrap gap-2 mt-4">
                @foreach($journey as $i => $step)
                <div class="journey-step flex-fill text-center" style="min-width:80px;">
                    <div class="step-circle mx-auto mb-1 d-flex align-items-center justify-content-center rounded-circle fw-bold"
                         style="width:36px;height:36px;font-size:15px;
                                background:{{ $step['done'] ? 'var(--theme-color,#2c9c3f)' : '#e9ecef' }};
                                color:{{ $step['done'] ? '#fff' : '#aaa' }};">
                        @if($step['done']) ✓ @else {{ $i + 1 }} @endif
                    </div>
                    <div class="fs-12 mt-1 {{ $step['done'] ? 'fw-600 text-black' : 'text-muted' }}" style="line-height:1.3;">
                        {{ $step['label'] }}
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Order summary (items) --}}
        <div class="row g-4 mb-4">
            <div class="col-md-5 offset-md-7">
                <div class="shadow-cus p-lg-4 p-3 rounded-3">
                    <h5 class="text-black mb-3">Order Summary</h5>
                    <ul class="d-flex flex-column gap-2 mb-3">
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Order ID</span>
                            <span class="fw-600">{{ $order->order_number }}</span>
                        </li>
                        <li class="d-flex justify-content-between fs-15">
                            <span class="text-muted">Payment</span>
                            <span class="fw-600 {{ $order->payment_status === 'paid' ? 'text-success' : 'text-warning' }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </li>
                    </ul>
                    @if($order->orderItems->count())
                    <div class="border-top pt-3">
                        <h6 class="mb-2">Items</h6>
                        @foreach($order->orderItems as $item)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            @if($item->product?->image)
                            <img src="{{ asset('uploads/products/thumbnails/' . $item->product->image) }}"
                                 class="rounded" style="width:40px;height:40px;object-fit:cover;"
                                 onerror="this.style.display='none'">
                            @endif
                            <div class="flex-grow-1">
                                <div class="fw-500 fs-14">{{ $item->product?->name ?? '—' }}</div>
                                @if($item->variant_label)
                                    <div class="fs-12 text-muted">{{ $item->variant_label }}</div>
                                @endif
                            </div>
                            <div class="text-end fs-14">
                                <div class="text-muted">× {{ $item->quantity }}</div>
                                <div class="fw-600">Rs {{ number_format($item->price * $item->quantity) }}</div>
                            </div>
                        </div>
                        @endforeach
                        <div class="border-top pt-2 d-flex justify-content-between fw-bold">
                            <span>Total</span>
                            <span>Rs {{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @elseif(request()->filled('tracking'))
            {{-- error shown above --}}
        @else
        <div class="text-center py-5 text-muted">
            <i class="fa-solid fa-truck-fast" style="font-size:52px;opacity:.15;"></i>
            <p class="mt-3 fs-16">Enter your tracking number above to see real-time delivery status.</p>
            <p class="fs-14">Example: <code>FB-2026-0001</code></p>
        </div>
        @endif

    </div>
</section>
@endsection
