@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Shipments</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Shipments</div></li>
            </ul>
        </div>

        @if(session('success'))
            <div class="alert alert-success mb-4">{{ session('success') }}</div>
        @endif

        {{-- Filters --}}
        <div class="wg-box mb-4">
            <form class="row g-3 align-items-end" method="GET">
                <div class="col-md-4">
                    <label class="form-label">Search (tracking / order ID / phone)</label>
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Track #, Order ID, Phone">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        @foreach(App\Models\Shipment::STATUSES as $key => $meta)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>{{ $meta['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Courier</label>
                    <select name="courier" class="form-select">
                        <option value="">All Couriers</option>
                        @foreach($couriers as $c)
                            <option value="{{ $c->id }}" {{ request('courier') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="tf-button style-1 w-100">Filter</button>
                    <a href="{{ route('admin.shipments.index') }}" class="tf-button" style="background:#eee;color:#333;">✕</a>
                </div>
            </form>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                <h5>All Shipments <span class="text-muted fs-6">({{ $shipments->total() }})</span></h5>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Order</th>
                            <th>Tracking Number</th>
                            <th>Courier</th>
                            <th>Destination</th>
                            <th class="text-center">Status</th>
                            <th>Booked On</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $s)
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>
                                <a href="{{ route('admin.order.details', $s->order_id) }}" class="fw-600">{{ $s->order?->order_number ?? 'FB-'.($s->order_id+1000) }}</a>
                                <div class="small text-muted">{{ $s->recipient_name }}</div>
                            </td>
                            <td>
                                <code class="fw-bold">{{ $s->tracking_number ?: '—' }}</code>
                                @if($s->tracking_number && $s->courier?->trackingUrl($s->tracking_number))
                                    <a href="{{ $s->courier->trackingUrl($s->tracking_number) }}" target="_blank" class="ms-1 small text-primary">↗</a>
                                @endif
                            </td>
                            <td>
                                @if($s->courier?->code === 'internal')
                                    <span class="badge bg-success">🚐 FB Delivery</span>
                                @else
                                    {{ $s->courier?->name ?? '—' }}
                                @endif
                            </td>
                            <td>{{ $s->destination_city }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $s->status_color }}">{{ $s->status_label }}</span>
                            </td>
                            <td>{{ $s->booking_date?->format('d M Y') ?? '—' }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('admin.shipments.show', $s) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    @if(in_array($s->status, ['pending','canceled']))
                                    <form action="{{ route('admin.shipments.destroy', $s) }}" method="POST"
                                          onsubmit="return confirm('Delete this shipment?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">✕</button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No shipments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination mt-3">
                {{ $shipments->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
