@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>🚐 Dispatch Board</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Dispatch Board</div></li>
            </ul>
        </div>

        @foreach(['success','warning','error','info'] as $type)
            @if(session($type))
                <div class="alert alert-{{ $type === 'error' ? 'danger' : $type }} mb-4">{{ session($type) }}</div>
            @endif
        @endforeach

        {{-- Status filter tabs --}}
        <div class="d-flex gap-2 mb-4 flex-wrap">
            @php
                $filterStatuses = [
                    ''                => 'All Active',
                    'booked'          => 'Pending Pickup',
                    'picked_up'       => 'Out from Store',
                    'out_for_delivery'=> 'On the Way',
                ];
            @endphp
            @foreach($filterStatuses as $val => $label)
            <a href="{{ route('admin.dispatch.index', $val ? ['status' => $val] : []) }}"
               class="tf-button {{ request('status', '') === $val ? 'style-1' : '' }}"
               style="{{ request('status', '') === $val ? '' : 'background:#eee;color:#333;' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>

        @if($shipments->isEmpty())
        <div class="wg-box text-center py-5 text-muted">
            <div style="font-size:52px;opacity:.2;">🚐</div>
            <p class="mt-3 fs-16">No active deliveries right now.</p>
        </div>
        @else

        <div class="row g-3">
            @foreach($shipments as $s)
            @php
                $statusColors = ['booked'=>'info','picked_up'=>'primary','out_for_delivery'=>'warning'];
                $statusLabels = ['booked'=>'Pending Pickup','picked_up'=>'Out from Store','out_for_delivery'=>'On the Way'];
                $nextActions  = [
                    'booked'          => ['status'=>'picked_up',        'label'=>'Out from Store', 'icon'=>'🏪', 'color'=>'primary'],
                    'picked_up'       => ['status'=>'out_for_delivery', 'label'=>'On the Way',     'icon'=>'🛵', 'color'=>'warning'],
                    'out_for_delivery'=> ['status'=>'delivered',        'label'=>'Delivered',      'icon'=>'✅', 'color'=>'success'],
                ];
                $next = $nextActions[$s->status] ?? null;
                $isUrgent = $s->estimated_delivery && $s->estimated_delivery->isToday();
                $isLate   = $s->estimated_delivery && $s->estimated_delivery->isPast() && $s->status !== 'delivered';
            @endphp
            <div class="col-xl-4 col-lg-6">
                <div class="wg-box h-100 d-flex flex-column" style="{{ $isLate ? 'border-left:4px solid #dc3545;' : ($isUrgent ? 'border-left:4px solid #fd7e14;' : 'border-left:4px solid #dee2e6;') }}">

                    {{-- Card header --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <a href="{{ route('admin.order.details', $s->order->id) }}" class="fw-bold fs-16 text-decoration-none">
                                {{ $s->order->order_number }}
                            </a>
                            <div class="small text-muted">{{ $s->tracking_number }}</div>
                        </div>
                        <span class="badge bg-{{ $statusColors[$s->status] ?? 'secondary' }}">
                            {{ $statusLabels[$s->status] ?? ucfirst($s->status) }}
                        </span>
                    </div>

                    {{-- Customer --}}
                    <div class="mb-3">
                        <div class="fw-600">{{ $s->recipient_name }}</div>
                        <div class="small text-muted">{{ $s->recipient_phone }}</div>
                        <div class="small text-muted mt-1">
                            <i class="fa-solid fa-location-dot me-1"></i>{{ $s->recipient_address }}
                        </div>
                    </div>

                    {{-- Rider & slot --}}
                    <div class="d-flex gap-3 mb-3 small">
                        @if($s->rider)
                        <div>
                            <div class="text-muted">Rider</div>
                            <div class="fw-600">{{ $s->rider->name }}</div>
                            <div class="text-muted">{{ $s->rider->vehicle_label }}</div>
                        </div>
                        @endif
                        @if($s->estimated_delivery)
                        <div>
                            <div class="text-muted">Delivery Date</div>
                            <div class="fw-600 {{ $isLate ? 'text-danger' : ($isUrgent ? 'text-warning' : '') }}">
                                {{ $s->estimated_delivery->format('d M Y') }}
                                @if($isLate) ⚠ Late @elseif($isUrgent) Today @endif
                            </div>
                            @if($s->delivery_time_slot)
                            <div class="text-muted">{{ $s->time_slot_label }}</div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <div class="mt-auto">
                        {{-- Timeline mini --}}
                        <div class="d-flex gap-1 mb-3">
                            @foreach(['booked','picked_up','out_for_delivery','delivered'] as $st)
                            @php
                                $stLabels = ['booked'=>'Assigned','picked_up'=>'Store','out_for_delivery'=>'Way','delivered'=>'Done'];
                                $stOrder  = array_search($s->status, ['booked','picked_up','out_for_delivery','delivered']) ?: 0;
                                $thisIdx  = array_search($st, ['booked','picked_up','out_for_delivery','delivered']) ?: 0;
                                $done     = $thisIdx <= $stOrder;
                            @endphp
                            <div class="flex-fill text-center" style="font-size:10px;">
                                <div style="height:4px;border-radius:2px;background:{{ $done ? '#28a745' : '#dee2e6' }};margin-bottom:3px;"></div>
                                <span class="{{ $done ? 'fw-600' : 'text-muted' }}">{{ $stLabels[$st] }}</span>
                            </div>
                            @endforeach
                        </div>

                        {{-- Action buttons --}}
                        <div class="d-flex gap-2">
                            @if($next)
                            <form action="{{ route('admin.dispatch.quick', $s) }}" method="POST" class="flex-fill">
                                @csrf
                                <input type="hidden" name="status" value="{{ $next['status'] }}">
                                <button type="submit"
                                        class="w-100 tf-button style-1"
                                        style="font-size:13px;"
                                        onclick="return confirm('Mark {{ $s->order->order_number }} as \'{{ $next['label'] }}\'?')">
                                    {{ $next['icon'] }} {{ $next['label'] }}
                                </button>
                            </form>
                            @else
                            <div class="flex-fill text-center text-muted small py-2">
                                <span class="badge bg-success fs-13">✅ Delivered</span>
                            </div>
                            @endif
                            <a href="{{ route('admin.shipments.show', $s) }}"
                               class="tf-button" style="background:#eee;color:#333;font-size:13px;white-space:nowrap;">
                               Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</div>
@endsection
