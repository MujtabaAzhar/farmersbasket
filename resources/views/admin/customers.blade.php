@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Customers</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Customers</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                <div class="wg-filter flex-grow">
                    <form class="form-search" method="GET" action="{{ route('admin.customers') }}">
                        <fieldset class="name">
                            <input type="text" placeholder="Search by name, email or phone..." name="search"
                                   value="{{ request('search') }}" tabindex="2">
                        </fieldset>
                        <div class="button-submit">
                            <button type="submit"><i class="icon-search"></i></button>
                        </div>
                    </form>
                </div>
                <div class="text-muted fs-14">
                    {{ $customers->total() }} customer{{ $customers->total() !== 1 ? 's' : '' }}
                </div>
            </div>

            <div class="wg-table table-all-user">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th class="text-center">Joined</th>
                                <th class="text-center">Orders</th>
                                <th class="text-center">Total Spent</th>
                                <th class="text-center">Last Order</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customers as $i => $customer)
                            @php
                                $lastOrder = $customer->orders->first();
                            @endphp
                            <tr>
                                <td class="text-muted fs-12">{{ $customers->firstItem() + $i }}</td>
                                <td>
                                    <div class="fw-600">{{ $customer->name }}</div>
                                </td>
                                <td>
                                    @if($customer->mobile)
                                        <div class="text-muted fs-12">{{ $customer->mobile }}</div>
                                    @endif
                                </td>
                                <td class="text-center fs-13">{{ $customer->created_at->format('d M Y') }}</td>
                                <td class="text-center">
                                    <span class="badge bg-secondary">{{ $customer->order_count }}</span>
                                </td>
                                <td class="text-center fw-600 fs-13">
                                    Rs {{ number_format($customer->total_spent ?? 0, 0) }}
                                </td>
                                <td class="text-center fs-12 text-muted">
                                    {{ $lastOrder ? $lastOrder->created_at->format('d M Y') : '—' }}
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.customer.detail', $customer->id) }}">
                                        <div class="list-icon-function view-icon">
                                            <div class="item eye"><i class="icon-eye"></i></div>
                                        </div>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">No customers found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="divider"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>
@endsection
