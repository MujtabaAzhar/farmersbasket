@extends('layouts.admin')
@section('content')
@php
    $order    = $shipment->order;
    $courier  = $shipment->courier;
    $trackings = $shipment->trackings;

    $quickSteps = [
        ['status' => 'picked_up',        'label' => 'Out from Store', 'icon' => '🏪', 'color' => 'primary',  'desc' => 'Order picked up from store and dispatched to rider.'],
        ['status' => 'out_for_delivery', 'label' => 'On the Way',     'icon' => '🛵', 'color' => 'warning',  'desc' => 'Rider is on the way to deliver your order.'],
        ['status' => 'delivered',        'label' => 'Delivered',      'icon' => '✅', 'color' => 'success',  'desc' => 'Order delivered successfully.'],
    ];
    $statusOrder = ['pending','booked','picked_up','in_transit','out_for_delivery','delivered'];
    $currentIdx  = array_search($shipment->status, $statusOrder) ?: 0;
@endphp

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>{{ $shipment->isInternal() ? '🚐 ' : '' }}Shipment #{{ $shipment->id }}</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.shipments.index') }}"><div class="text-tiny">Shipments</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">#{{ $shipment->id }}</div></li>
            </ul>
        </div>

        @foreach(['success','warning','error','info'] as $type)
            @if(session($type))
                <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} mb-4">{{ session($type) }}</div>
            @endif
        @endforeach

        {{-- Status bar --}}
        <div class="wg-box mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-{{ $shipment->status_color }}" style="font-size:14px;padding:6px 14px;">
                    {{ $shipment->status_label }}
                </span>
                <span class="text-muted small">Order
                    <a href="{{ route('admin.order.details', $order->id) }}" class="fw-bold">{{ $order->order_number }}</a>
                </span>
                @if($shipment->isInternal())
                    <a href="{{ route('admin.dispatch.index') }}" class="small text-primary">← Dispatch Board</a>
                @endif
            </div>
            <div class="d-flex gap-2">
                @if($shipment->tracking_number && !$shipment->isInternal())
                <form action="{{ route('admin.shipments.refresh', $shipment) }}" method="POST">
                    @csrf
                    <button class="tf-button">↻ Refresh from API</button>
                </form>
                @endif
                @if($shipment->tracking_number && $courier?->trackingUrl($shipment->tracking_number))
                <a href="{{ $courier->trackingUrl($shipment->tracking_number) }}" target="_blank"
                   class="tf-button" style="background:#17a2b8;color:#fff;">
                    Track on {{ $courier->name }} ↗
                </a>
                @endif
            </div>
        </div>

        {{-- ── INTERNAL DELIVERY: Quick Action Buttons ── --}}
        @if($shipment->isInternal() && !in_array($shipment->status, ['delivered','canceled','returned']))
        <div class="wg-box mb-4">
            <h5 class="mb-1">🚐 Delivery Actions</h5>
            <p class="text-muted small mb-4">Tap the button when the delivery reaches that stage. Status updates instantly.</p>

            <div class="row g-3">
                @foreach($quickSteps as $step)
                @php
                    $stepIdx  = array_search($step['status'], $statusOrder) ?: 0;
                    $isDone   = $stepIdx <= $currentIdx && $shipment->status !== 'pending' && $shipment->status !== 'booked';
                    $isDone   = in_array($step['status'], ['picked_up','out_for_delivery','delivered'])
                                && (array_search($shipment->status, $statusOrder) >= $stepIdx);
                    $isNext   = ($stepIdx === $currentIdx + 1) || ($shipment->status === 'booked' && $step['status'] === 'picked_up');
                    $canClick = !$isDone && ($isNext || ($stepIdx <= $currentIdx + 2));
                @endphp
                <div class="col-md-4">
                    @if($isDone)
                    {{-- Already done --}}
                    <div class="border rounded-3 p-4 text-center" style="background:#f0fff4;border-color:#28a745!important;">
                        <div style="font-size:36px;">{{ $step['icon'] }}</div>
                        <div class="fw-bold mt-2 text-success">{{ $step['label'] }}</div>
                        @php $evt = $trackings->firstWhere('status', $step['status']); @endphp
                        @if($evt)
                        <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($evt->event_time)->format('d M Y, h:i A') }}</div>
                        @endif
                        <div class="mt-2"><span class="badge bg-success">Done ✓</span></div>
                    </div>
                    @elseif($isNext)
                    {{-- Next action --}}
                    <form action="{{ route('admin.shipments.status', $shipment) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="{{ $step['status'] }}">
                        <input type="hidden" name="description" value="{{ $step['desc'] }}">
                        <button type="submit" class="w-100 border-0 rounded-3 p-4 text-center cursor-pointer"
                                style="background:#fff3cd;border:2px solid #ffc107!important;cursor:pointer;transition:all .15s;"
                                onmouseover="this.style.background='#ffe083'" onmouseout="this.style.background='#fff3cd'"
                                onclick="return confirm('Mark as \'{{ $step['label'] }}\'?')">
                            <div style="font-size:36px;">{{ $step['icon'] }}</div>
                            <div class="fw-bold mt-2" style="color:#856404;">{{ $step['label'] }}</div>
                            <div class="small text-muted mt-1">Tap to update</div>
                            <div class="mt-2"><span class="badge bg-warning text-dark">Next Step →</span></div>
                        </button>
                    </form>
                    @else
                    {{-- Upcoming --}}
                    <div class="border rounded-3 p-4 text-center" style="background:#f8f9fa;opacity:.55;">
                        <div style="font-size:36px;">{{ $step['icon'] }}</div>
                        <div class="fw-bold mt-2 text-muted">{{ $step['label'] }}</div>
                        <div class="small text-muted mt-1">Upcoming</div>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="row g-4">

            {{-- Left column --}}
            <div class="col-md-5">

                {{-- Shipment info --}}
                <div class="wg-box mb-4">
                    <h5 class="mb-3">Shipment Info</h5>
                    <table class="table table-sm table-borderless mb-0">
                        <tr><td class="text-muted" style="width:42%">Tracking #</td>
                            <td><code class="fw-bold">{{ $shipment->tracking_number ?: '—' }}</code></td></tr>
                        @if(!$shipment->isInternal())
                        <tr><td class="text-muted">CN Number</td>
                            <td>{{ $shipment->cn_number ?: '—' }}</td></tr>
                        @endif
                        <tr><td class="text-muted">Type</td>
                            <td>
                                @if($shipment->isInternal())
                                    <span class="badge bg-success">🚐 Farmer's Basket</span>
                                @else
                                    <span class="badge bg-info text-dark">{{ $courier?->name }}</span>
                                @endif
                            </td></tr>
                        @if($shipment->isInternal() && $shipment->rider)
                        <tr><td class="text-muted">Rider</td>
                            <td class="fw-600">{{ $shipment->rider->name }}</td></tr>
                        <tr><td class="text-muted">Vehicle</td>
                            <td>{{ $shipment->rider->vehicle_label }}</td></tr>
                        @endif
                        @if($shipment->isInternal() && $shipment->delivery_time_slot)
                        <tr><td class="text-muted">Time Slot</td>
                            <td>{{ $shipment->time_slot_label }}</td></tr>
                        @endif
                        <tr><td class="text-muted">Booked On</td>
                            <td>{{ $shipment->booking_date?->format('d M Y') ?? '—' }}</td></tr>
                        <tr><td class="text-muted">Est. Delivery</td>
                            <td>{{ $shipment->estimated_delivery?->format('d M Y') ?? '—' }}</td></tr>
                        @if($shipment->actual_delivery)
                        <tr><td class="text-muted">Delivered</td>
                            <td class="text-success fw-bold">{{ $shipment->actual_delivery->format('d M Y') }}</td></tr>
                        @endif
                        <tr><td class="text-muted">Dispatched By</td>
                            <td>{{ $shipment->bookedBy?->name ?? '—' }}</td></tr>
                        @if(!$shipment->isInternal())
                        <tr><td class="text-muted">Last Tracked</td>
                            <td>{{ $shipment->last_tracked_at?->diffForHumans() ?? 'Never' }}</td></tr>
                        @endif
                    </table>
                </div>

                {{-- Recipient --}}
                <div class="wg-box mb-4">
                    <h5 class="mb-3">Recipient</h5>
                    <p class="mb-1 fw-600">{{ $shipment->recipient_name }}</p>
                    <p class="mb-1">{{ $shipment->recipient_phone }}</p>
                    <p class="mb-1 text-muted small">{{ $shipment->recipient_address }}</p>
                    <p class="mb-0 small"><span class="text-muted">{{ $shipment->origin_city }}</span> → <span class="fw-600">{{ $shipment->destination_city }}</span></p>
                </div>

                {{-- Order items --}}
                <div class="wg-box">
                    <h5 class="mb-3">Order Items</h5>
                    @foreach($order->orderItems as $item)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom small">
                        <div>
                            <div class="fw-500">{{ $item->product?->name ?? 'Deleted' }}</div>
                            @if($item->variant_label)
                                <span class="text-muted">{{ $item->variant_label }}</span>
                            @endif
                        </div>
                        <div class="text-end">
                            <div class="text-muted">× {{ $item->quantity }}</div>
                            <div class="fw-bold">Rs {{ number_format($item->price * $item->quantity) }}</div>
                        </div>
                    </div>
                    @endforeach
                    <div class="d-flex justify-content-between fw-bold pt-2">
                        <span>Total</span>
                        <span>Rs {{ number_format($order->total, 2) }}</span>
                    </div>
                </div>

            </div>

            {{-- Right column --}}
            <div class="col-md-7">

                {{-- Progress bar --}}
                <div class="wg-box mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Delivery Progress</span>
                        <span class="fw-bold text-success">{{ $shipment->progressPercent() }}%</span>
                    </div>
                    <div class="progress" style="height:10px;border-radius:8px;">
                        <div class="progress-bar bg-success" style="width:{{ $shipment->progressPercent() }}%;border-radius:8px;"></div>
                    </div>
                    @if($shipment->isInternal())
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>Assigned</span><span>Out from Store</span><span>On the Way</span><span>Delivered</span>
                    </div>
                    @else
                    <div class="d-flex justify-content-between mt-2 small text-muted">
                        <span>Booked</span><span>Picked Up</span><span>In Transit</span><span>Out for Delivery</span><span>Delivered</span>
                    </div>
                    @endif
                </div>

                {{-- Tracking timeline --}}
                <div class="wg-box mb-4">
                    <h5 class="mb-3">Delivery Timeline</h5>
                    @if($trackings->isEmpty())
                        <p class="text-muted small">No events yet.</p>
                    @else
                    <div class="timeline">
                        @foreach($trackings as $t)
                        @php
                            $dot = match($t->status) {
                                'delivered'   => '#28a745',
                                'failed'      => '#dc3545',
                                'out_for_delivery', 'picked_up' => '#fd7e14',
                                default       => '#007bff',
                            };
                            $readableStatus = match($t->status) {
                                'picked_up'        => 'Out from Store',
                                'out_for_delivery' => 'On the Way',
                                default => ucwords(str_replace('_', ' ', $t->status)),
                            };
                        @endphp
                        <div class="timeline-event d-flex gap-3 pb-3" style="position:relative;">
                            <div style="flex-shrink:0;margin-top:4px;">
                                <div style="width:12px;height:12px;border-radius:50%;background:{{ $dot }};"></div>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ $readableStatus }}</span>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($t->event_time)->format('d M Y, h:i A') }}</small>
                                </div>
                                <div class="text-muted small">{{ $t->description }}</div>
                                @if($t->location)
                                    <div class="small"><i class="fa-solid fa-location-dot me-1 text-danger"></i>{{ $t->location }}</div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                {{-- Manual / custom update --}}
                <div class="wg-box">
                    <h5 class="mb-3">{{ $shipment->isInternal() ? 'Custom Update' : 'Update Status' }}</h5>
                    @if($shipment->isInternal())
                    <p class="text-muted small mb-3">Use this for custom notes or edge cases (e.g. failed attempt, rescheduled).</p>
                    @endif
                    <form action="{{ route('admin.shipments.status', $shipment) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Status *</label>
                                <div class="select">
                                    <select name="status" required>
                                        @foreach(App\Models\Shipment::STATUSES as $key => $meta)
                                            <option value="{{ $key }}" {{ $shipment->status === $key ? 'selected' : '' }}>
                                                @if($shipment->isInternal())
                                                    {{ match($key) { 'picked_up' => 'Out from Store', 'out_for_delivery' => 'On the Way', 'booked' => 'Assigned / Pending Pickup', default => $meta['label'] } }}
                                                @else
                                                    {{ $meta['label'] }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Location <small class="text-muted">(optional)</small></label>
                                <input type="text" name="location" class="form-control" placeholder="Area / landmark">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">Note <small class="text-muted">(optional)</small></label>
                                <input type="text" name="description" class="form-control" placeholder="Leave blank for auto-generated note">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Event Time</label>
                                <input type="datetime-local" name="event_time" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}">
                            </div>
                            <div class="col-12">
                                <button type="submit" class="tf-button style-1">Update Status</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<style>
    .timeline { position: relative; }
    .timeline-event + .timeline-event::before { content:''; position:absolute; left:5px; top:-12px; width:2px; height:12px; background:#dee2e6; }
    .timeline-event { position: relative; }
</style>
@endsection
