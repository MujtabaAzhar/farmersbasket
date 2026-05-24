@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>POS Sessions</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">POS Sessions</div></li>
            </ul>
        </div>

        {{-- Summary Cards --}}
        <div class="row gap20 mb-20">
            <div class="col-md-3">
                <div class="wg-box text-center py-14">
                    <div class="text-tiny text-secondary mb-4">Open Sessions</div>
                    <div style="font-size:28px; font-weight:800; color:#2ecc71;">{{ $openCount }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wg-box text-center py-14">
                    <div class="text-tiny text-secondary mb-4">Total Today</div>
                    <div style="font-size:28px; font-weight:800;">{{ $todayCount }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="wg-box text-center py-14">
                    <div class="text-tiny text-secondary mb-4">Sales Today (POS)</div>
                    <div style="font-size:24px; font-weight:800;">Rs {{ number_format($todaySales, 0) }}</div>
                </div>
            </div>
        </div>

        <div class="wg-box">
            <h5 class="mb-20">All Sessions</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cashier</th>
                            <th>Branch</th>
                            <th>Opened At</th>
                            <th>Closed At</th>
                            <th class="text-right">Opening</th>
                            <th class="text-right">Expected</th>
                            <th class="text-right">Counted</th>
                            <th class="text-right">Variance</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $s)
                        @php
                            $variance = ($s->closing_balance !== null && $s->expected_cash !== null)
                                ? $s->closing_balance - $s->expected_cash
                                : null;
                        @endphp
                        <tr>
                            <td>{{ $s->id }}</td>
                            <td>{{ $s->cashier?->name ?? '—' }}</td>
                            <td>{{ $s->branch?->name ?? '—' }}</td>
                            <td>{{ $s->opened_at->format('d M Y h:i A') }}</td>
                            <td>{{ $s->closed_at?->format('d M Y h:i A') ?? '—' }}</td>
                            <td class="text-right">Rs {{ number_format($s->opening_balance, 0) }}</td>
                            <td class="text-right">{{ $s->expected_cash !== null ? 'Rs '.number_format($s->expected_cash, 0) : '—' }}</td>
                            <td class="text-right">{{ $s->closing_balance !== null ? 'Rs '.number_format($s->closing_balance, 0) : '—' }}</td>
                            <td class="text-right">
                                @if($variance !== null)
                                    <span style="color: {{ $variance >= 0 ? '#2ecc71' : '#e74c3c' }}; font-weight:600;">
                                        {{ $variance >= 0 ? '+' : '' }}Rs {{ number_format($variance, 0) }}
                                    </span>
                                @else —
                                @endif
                            </td>
                            <td class="text-center">
                                @if($s->status === 'open')
                                    <span class="badge bg-success">Open</span>
                                @else
                                    <span class="badge bg-secondary">Closed</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center py-20">No sessions found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-20">{{ $sessions->links() }}</div>
        </div>
    </div>
</div>
@endsection
