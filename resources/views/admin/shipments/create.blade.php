@extends('layouts.admin')
@section('content')

<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Create Shipment — {{ $order->order_number }}</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.shipments.index') }}"><div class="text-tiny">Shipments</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Create</div></li>
            </ul>
        </div>

        @if($existing)
        <div class="alert alert-warning mb-4">
            ⚠ {{ $order->order_number }} already has an active shipment
            (<a href="{{ route('admin.shipments.show', $existing) }}" class="fw-bold">
                {{ $existing->tracking_number ?: '#'.$existing->id }}
            </a> — {{ $existing->status_label }}).
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger mb-4">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="row g-4">

            {{-- Order summary sidebar --}}
            <div class="col-md-4">
                <div class="wg-box">
                    <h5 class="mb-3">{{ $order->order_number }}</h5>
                    <p class="mb-1"><b>Customer:</b> {{ $order->name ?: 'Walk-in' }}</p>
                    <p class="mb-1"><b>Phone:</b> {{ $order->phone }}</p>
                    <p class="mb-1"><b>City:</b> {{ $order->city }}</p>
                    <p class="mb-3"><b>Address:</b> {{ $order->address }}</p>
                    <p class="mb-3 fw-bold">Total: Rs {{ number_format($order->total, 2) }}</p>
                    <hr>
                    <h6 class="mb-2">Items</h6>
                    @foreach($order->orderItems as $item)
                    <div class="d-flex justify-content-between align-items-start py-1 border-bottom small">
                        <div>
                            <div class="fw-500">{{ $item->product?->name ?? 'Deleted' }}</div>
                            @if($item->variant_label)
                                <span class="text-muted">{{ $item->variant_label }}</span>
                            @endif
                        </div>
                        <div class="text-end ms-2">
                            <div class="text-muted">× {{ $item->quantity }}</div>
                            <div class="fw-bold">Rs {{ number_format($item->price * $item->quantity) }}</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Main form --}}
            <div class="col-md-8">
                <div class="wg-box">
                    <form action="{{ route('admin.shipments.store') }}" method="POST" id="shipment-form">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <input type="hidden" name="courier_service_id" id="selected_courier_id"
                               value="{{ old('courier_service_id', $couriers->first()?->id) }}">

                        {{-- ── STEP 1: Shipment Type ── --}}
                        <h5 class="mb-3">Step 1 — Shipment Type</h5>
                        <div class="row g-3 mb-4" id="courier-cards">
                            @foreach($couriers as $c)
                            @php $isInternal = $c->code === 'internal'; @endphp
                            <div class="col-md-6">
                                <div class="courier-type-card border rounded-3 p-3 cursor-pointer
                                            {{ old('courier_service_id', $couriers->first()?->id) == $c->id ? 'selected' : '' }}"
                                     data-courier-id="{{ $c->id }}"
                                     data-courier-code="{{ $c->code }}"
                                     onclick="selectCourier({{ $c->id }}, '{{ $c->code }}')">
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div class="courier-dot"></div>
                                        @if($isInternal)
                                            <span class="fw-bold">🚐 Farmer's Basket Delivery</span>
                                        @elseif($c->code === 'leopards')
                                            <span class="fw-bold">🐆 Leopards Courier</span>
                                        @elseif($c->code === 'tcs')
                                            <span class="fw-bold">📦 TCS Couriers</span>
                                        @else
                                            <span class="fw-bold">🚛 {{ $c->name }}</span>
                                        @endif
                                    </div>
                                    <div class="small text-muted">
                                        @if($isInternal)
                                            Own rider • Generates FB-YYYY-XXXX tracking
                                        @else
                                            {{ $c->isConfigured() ? '✅ API configured' : '⚠ Manual tracking' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        {{-- ── STEP 2a: FB Internal Delivery ── --}}
                        <div id="panel-internal" style="display:none;">
                            <h5 class="mb-3">Step 2 — Rider & Schedule</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Delivery Rider <span class="text-danger">*</span></label>
                                    <div class="select">
                                        <select name="rider_id" id="rider_id">
                                            <option value="">— Select Rider —</option>
                                            @foreach($riders as $rider)
                                            <option value="{{ $rider->id }}" {{ old('rider_id') == $rider->id ? 'selected' : '' }}>
                                                {{ $rider->name }}
                                                ({{ $rider->vehicle_label }})
                                                {{ $rider->branch ? '— '.$rider->branch->name : '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if($riders->isEmpty())
                                        <div class="form-text text-warning">
                                            No active riders.
                                            <a href="{{ route('admin.riders.index') }}" target="_blank">Add a rider →</a>
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Vehicle <span class="text-danger">*</span></label>
                                    <div class="select">
                                        <select name="vehicle_type" id="vehicle_type">
                                            <option value="">— Select Vehicle —</option>
                                            @foreach(App\Models\Rider::VEHICLES as $key => $label)
                                            <option value="{{ $key }}" {{ old('vehicle_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Delivery Date <span class="text-danger">*</span></label>
                                    <input type="date" name="estimated_delivery_date" id="delivery_date" class="form-control"
                                           value="{{ old('estimated_delivery_date') }}" min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-600">Time Slot <span class="text-danger">*</span></label>
                                    <div class="select">
                                        <select name="delivery_time_slot" id="delivery_time_slot">
                                            <option value="">— Select Time —</option>
                                            @foreach(App\Models\Shipment::TIME_SLOTS as $key => $label)
                                            <option value="{{ $key }}" {{ old('delivery_time_slot') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── STEP 2b: External Courier ── --}}
                        <div id="panel-courier" style="display:none;">
                            <h5 class="mb-3">Step 2 — Parcel Details</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label">Weight (KG) <span class="text-danger">*</span></label>
                                    <input type="number" name="weight" class="form-control" step="0.1" min="0.1"
                                           value="{{ old('weight', 0.5) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Pieces <span class="text-danger">*</span></label>
                                    <input type="number" name="pieces" class="form-control" min="1"
                                           value="{{ old('pieces', 1) }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Declared Value (Rs) <span class="text-danger">*</span></label>
                                    <input type="number" name="declared_value" class="form-control" min="0"
                                           value="{{ old('declared_value', $order->total) }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Est. Delivery Date</label>
                                    <input type="date" name="estimated_delivery_date" class="form-control"
                                           value="{{ old('estimated_delivery_date') }}" min="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Special Instructions</label>
                                    <input type="text" name="special_instructions" class="form-control"
                                           value="{{ old('special_instructions', 'Handle with care — Fresh Fruit') }}"
                                           maxlength="500">
                                </div>
                            </div>
                        </div>

                        {{-- ── STEP 3: Recipient & Route (always shown) ── --}}
                        <div id="panel-recipient">
                            <h5 class="mb-3">Step 3 — Recipient & Route</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                    <input type="text" name="recipient_name" class="form-control"
                                           value="{{ old('recipient_name', $order->name) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="recipient_phone" class="form-control"
                                           value="{{ old('recipient_phone', $order->phone) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Delivery Address <span class="text-danger">*</span></label>
                                    <input type="text" name="recipient_address" class="form-control"
                                           value="{{ old('recipient_address', trim($order->address . ($order->locality ? ', '.$order->locality : ''))) }}"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Origin City <span class="text-danger">*</span></label>
                                    <input type="text" name="origin_city" class="form-control"
                                           value="{{ old('origin_city', 'Multan') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Destination City <span class="text-danger">*</span></label>
                                    <input type="text" name="destination_city" class="form-control"
                                           value="{{ old('destination_city', $order->city) }}" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Internal Notes</label>
                                    <input type="text" name="notes" class="form-control"
                                           value="{{ old('notes') }}" maxlength="500">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="tf-button style-1" id="submit-btn">
                                Create Shipment
                            </button>
                            <a href="{{ route('admin.order.details', $order->id) }}"
                               class="tf-button" style="background:#eee;color:#333;">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .courier-type-card { cursor: pointer; transition: border-color .15s, background .15s; border-color: #ddd !important; }
    .courier-type-card:hover { border-color: #aaa !important; }
    .courier-type-card.selected { border-color: #28a745 !important; background: #f0fff4; }
    .courier-dot { width: 14px; height: 14px; border-radius: 50%; border: 2px solid #ccc; flex-shrink: 0; transition: all .15s; }
    .courier-type-card.selected .courier-dot { background: #28a745; border-color: #28a745; }
</style>

<script>
@php
    $internalId  = $couriers->firstWhere('code','internal')?->id;
    $firstId     = $couriers->first()?->id;
    $firstCode   = $couriers->first()?->code;
@endphp
var internalCourierId = {{ $internalId ?? 'null' }};

function selectCourier(id, code) {
    document.getElementById('selected_courier_id').value = id;

    document.querySelectorAll('.courier-type-card').forEach(function(c){
        c.classList.remove('selected');
    });
    document.querySelector('[data-courier-id="' + id + '"]').classList.add('selected');

    var isInternal = (code === 'internal');
    document.getElementById('panel-internal').style.display = isInternal ? '' : 'none';
    document.getElementById('panel-courier').style.display  = isInternal ? 'none' : '';

    document.getElementById('submit-btn').textContent = isInternal
        ? '🚐 Assign & Dispatch'
        : '📦 Book Shipment';
}

// Init on load
(function(){
    var firstId   = {{ $couriers->first()?->id ?? 'null' }};
    var firstCode = '{{ $couriers->first()?->code ?? '' }}';
    if (firstId) selectCourier(firstId, firstCode);
})();
</script>
@endsection
