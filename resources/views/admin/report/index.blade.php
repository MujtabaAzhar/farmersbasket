@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Generate Report</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Report</div></li>
            </ul>
        </div>

        <div class="wg-box mb-20">
            <h5 class="mb-4">Daily / Period Summary Report</h5>

            <form action="{{ route('admin.report.pdf') }}" method="GET" target="_blank">

                <div class="row gap20">
                    <div class="col-md-3">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Date From <span class="tf-color-1">*</span></div>
                            <input type="date" name="date_from" class="flex-grow"
                                   value="{{ request('date_from', now()->toDateString()) }}" required>
                        </fieldset>
                    </div>
                    <div class="col-md-3">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Date To <span class="tf-color-1">*</span></div>
                            <input type="date" name="date_to" class="flex-grow"
                                   value="{{ request('date_to', now()->toDateString()) }}" required>
                        </fieldset>
                    </div>
                    <div class="col-md-3">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Cashier (optional)</div>
                            <select name="cashier_id" class="flex-grow">
                                <option value="">— All Cashiers —</option>
                                @foreach($cashiers as $c)
                                    <option value="{{ $c->id }}" {{ request('cashier_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->name }}
                                    </option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                    <div class="col-md-3">
                        <fieldset class="name mb-24">
                            <div class="body-title mb-10">Branch (optional)</div>
                            <select name="branch_id" class="flex-grow">
                                <option value="">— All Branches —</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>
                                        {{ $b->name }}
                                    </option>
                                @endforeach
                            </select>
                        </fieldset>
                    </div>
                </div>

                <div class="d-flex gap-3 align-items-center mt-2">
                    <button type="submit" class="tf-button style-1 w208">
                        <i class="icon-file-text me-2"></i> Generate PDF Report
                    </button>
                    <a href="{{ route('admin.expenses') }}" class="tf-button style-2 w208">
                        <i class="icon-dollar-sign me-2"></i> Manage Expenses
                    </a>
                </div>
            </form>
        </div>

        {{-- Quick stats for today --}}
        @php
            $todayOrders  = \App\Models\Order::whereDate('created_at', today())->where('is_hold', false)->get();
            $todaySales   = $todayOrders->sum('total');
            $todayCount   = $todayOrders->count();
            $todayPos     = $todayOrders->where('source', 'pos')->count();
            $todayOnline  = $todayOrders->where('source', '!=', 'pos')->count();
            $todayExpenses = \App\Models\Expense::whereDate('expense_date', today())->sum('amount');
        @endphp

        <div class="wg-box">
            <h5 class="mb-4">Today's Snapshot — {{ now()->format('d M Y') }}</h5>
            <div class="row g-3">
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#f0fdf4;border:1px solid #bbf7d0;">
                        <div style="font-size:22px;font-weight:800;color:#16a34a;">{{ $todayCount }}</div>
                        <div class="text-muted" style="font-size:12px;">Total Orders</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#eff6ff;border:1px solid #bfdbfe;">
                        <div style="font-size:22px;font-weight:800;color:#1d4ed8;">Rs {{ number_format($todaySales, 0) }}</div>
                        <div class="text-muted" style="font-size:12px;">Total Sales</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#fefce8;border:1px solid #fde68a;">
                        <div style="font-size:22px;font-weight:800;color:#92400e;">{{ $todayPos }}</div>
                        <div class="text-muted" style="font-size:12px;">POS Orders</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#fdf4ff;border:1px solid #e9d5ff;">
                        <div style="font-size:22px;font-weight:800;color:#7e22ce;">{{ $todayOnline }}</div>
                        <div class="text-muted" style="font-size:12px;">Online Orders</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#fff1f2;border:1px solid #fecdd3;">
                        <div style="font-size:22px;font-weight:800;color:#be123c;">Rs {{ number_format($todayExpenses, 0) }}</div>
                        <div class="text-muted" style="font-size:12px;">Expenses</div>
                    </div>
                </div>
                <div class="col-md-2 col-sm-4 col-6">
                    <div class="text-center p-3 rounded" style="background:#f0fdf4;border:1px solid #86efac;">
                        <div style="font-size:22px;font-weight:800;color:#15803d;">Rs {{ number_format($todaySales - $todayExpenses, 0) }}</div>
                        <div class="text-muted" style="font-size:12px;">Net</div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
