@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Customer: {{ $customer->name }}</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.customers') }}"><div class="text-tiny">Customers</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">{{ $customer->name }}</div></li>
            </ul>
        </div>

        <div class="row g-4 mb-4">

            {{-- Profile Card --}}
            <div class="col-md-4">
                <div class="wg-box h-100">
                    <h6 class="mb-3" style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;">
                        Profile
                    </h6>
                    <div class="mb-2">
                        <div class="text-muted fs-12">Name</div>
                        <div class="fw-600">{{ $customer->name }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted fs-12">Email</div>
                        <div>{{ $customer->email }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted fs-12">Phone</div>
                        <div>{{ $customer->mobile ?: '—' }}</div>
                    </div>
                    <div class="mb-2">
                        <div class="text-muted fs-12">Joined</div>
                        <div>{{ $customer->created_at->format('d M Y, H:i') }}</div>
                    </div>
                </div>
            </div>

            {{-- Stats Card --}}
            <div class="col-md-4">
                <div class="wg-box h-100">
                    <h6 class="mb-3" style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;">
                        Order Stats
                    </h6>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-13">Total Orders</span>
                        <span class="badge bg-secondary fs-14">{{ $orderCount }}</span>
                    </div>
                    <div class="mb-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted fs-13">Total Spent (delivered)</span>
                        <span class="fw-700 fs-14">Rs {{ number_format($totalSpent, 0) }}</span>
                    </div>
                    @php
                        $delivered = $orders->where('status','delivered')->count();
                        $canceled  = $orders->where('status','canceled')->count();
                        $pending   = $orders->whereNotIn('status',['delivered','canceled','returned'])->count();
                    @endphp
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted fs-13">Delivered</span>
                        <span class="badge bg-success">{{ $delivered }}</span>
                    </div>
                    <div class="mb-2 d-flex justify-content-between">
                        <span class="text-muted fs-13">Canceled</span>
                        <span class="badge bg-danger">{{ $canceled }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted fs-13">In Progress</span>
                        <span class="badge bg-warning text-dark">{{ $pending }}</span>
                    </div>
                </div>
            </div>

            {{-- Addresses Card --}}
            <div class="col-md-4">
                <div class="wg-box h-100">
                    <h6 class="mb-3" style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;">
                        Saved Addresses
                    </h6>
                    @forelse($addresses as $addr)
                    <div class="mb-3 p-2 rounded" style="border:1px solid #eee;{{ $addr->is_default ? 'border-color:#2ecc71;background:#f0fdf4;' : '' }}">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <span class="fw-600 fs-13">{{ $addr->title ?: 'Address' }}</span>
                            @if($addr->is_default)
                                <span class="badge" style="background:#2ecc71;font-size:10px;">Default</span>
                            @endif
                        </div>
                        <div class="fs-12 text-muted">{{ $addr->address }}</div>
                        <div class="fs-12 text-muted">{{ $addr->city }}</div>
                    </div>
                    @empty
                    <p class="text-muted fs-13">No saved addresses.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Order History --}}
        <div class="wg-box">
            <h6 class="mb-3" style="font-size:13px;font-weight:700;text-transform:uppercase;color:#888;">
                Order History ({{ $orderCount }})
            </h6>
            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Order No</th>
                            <th class="text-center">Date</th>
                            <th class="text-center">Items</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Payment</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Source</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        @php
                            $sc = ['ordered'=>'warning','confirmed'=>'info','packed'=>'secondary','shipped'=>'primary','delivered'=>'success','canceled'=>'danger','returned'=>'dark'];
                        @endphp
                        <tr>
                            <td class="fw-600">{{ $order->order_number }}</td>
                            <td class="text-center fs-13">{{ $order->created_at->format('d M Y, H:i') }}</td>
                            <td class="text-center">{{ $order->orderItems->count() }}</td>
                            <td class="text-center fw-600">Rs {{ number_format($order->total, 0) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst($order->payment_status ?? 'pending') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $sc[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="text-center">
                                @if($order->source === 'pos')
                                    <span class="badge bg-info">POS</span>
                                @else
                                    <span class="badge bg-light text-dark" style="border:1px solid #ddd;">Online</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}">
                                    <div class="list-icon-function view-icon">
                                        <div class="item eye"><i class="icon-eye"></i></div>
                                    </div>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No orders yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
