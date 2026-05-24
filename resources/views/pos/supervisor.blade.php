@extends('layouts.pos')

@push('styles')
<style>
    .sv-wrap { flex: 1; overflow-y: auto; padding: 20px; }
    .sv-header { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
    .sv-header h4 { font-size: 16px; font-weight: 700; color: #1a1f2e; margin: 0; }
    .btn-back { background: none; border: 1px solid #ddd; border-radius: 8px; padding: 7px 14px; font-size: 13px; cursor: pointer; color: #555; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 18px; }
    .stat-card { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 16px 18px; }
    .stat-card .stat-label { font-size: 11px; font-weight: 600; color: #888; text-transform: uppercase; margin-bottom: 4px; }
    .stat-card .stat-val { font-size: 22px; font-weight: 800; color: #1a1f2e; }
    .stat-card .stat-sub { font-size: 11px; color: #aaa; margin-top: 2px; }

    .sv-section { background: #fff; border: 1px solid #e8e8e8; border-radius: 10px; padding: 16px 18px; margin-bottom: 16px; }
    .sv-section h5 { font-size: 13px; font-weight: 700; color: #1a1f2e; margin-bottom: 12px; text-transform: uppercase; }

    .session-row { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f5f5f5; font-size: 13px; }
    .session-row:last-child { border-bottom: none; }
    .session-row .sr-name { flex: 1; font-weight: 600; }
    .session-row .sr-branch { color: #888; font-size: 12px; }
    .session-row .sr-time { color: #888; font-size: 12px; }

    .order-row { display: flex; align-items: center; gap: 10px; padding: 7px 0; border-bottom: 1px solid #f5f5f5; font-size: 12px; }
    .order-row:last-child { border-bottom: none; }
    .order-row .or-id { font-weight: 700; color: #1a1f2e; min-width: 60px; }
    .order-row .or-cashier { flex: 1; color: #555; }
    .order-row .or-total { font-weight: 700; color: #2ecc71; min-width: 80px; text-align: right; }
    .or-method { display: inline-block; background: #f0f0f0; border-radius: 20px; padding: 1px 8px; font-size: 11px; }
    .or-method.cash { background: #d4edda; color: #155724; }
    .or-method.card { background: #cce5ff; color: #004085; }
    .or-method.bank_transfer { background: #fff3cd; color: #856404; }

    .empty-state { text-align:center; color:#aaa; padding:20px; font-size:13px; }
</style>
@endpush

@section('content')
<div class="sv-wrap">
    <div class="sv-header">
        <button class="btn-back" onclick="window.location='{{ route('pos.index') }}'">← Back to POS</button>
        <h4>Supervisor Dashboard
            @if($branch) — {{ $branch->name }} @endif
        </h4>
    </div>

    {{-- Stats --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Today's Sales</div>
            <div class="stat-val">Rs {{ number_format($todaySales, 0) }}</div>
            <div class="stat-sub">Total revenue collected</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Orders Today</div>
            <div class="stat-val">{{ $todayOrders }}</div>
            <div class="stat-sub">Completed transactions</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Open Sessions</div>
            <div class="stat-val">{{ $openSessions->count() }}</div>
            <div class="stat-sub">Active cashier shifts</div>
        </div>
    </div>

    {{-- Open Sessions --}}
    <div class="sv-section">
        <h5>Active Cashier Sessions</h5>
        @if($openSessions->isEmpty())
        <div class="empty-state">No active sessions.</div>
        @else
        @foreach($openSessions as $s)
        <div class="session-row">
            <div class="sr-name">{{ $s->cashier?->name ?? 'Unknown' }}</div>
            <div class="sr-branch">{{ $s->branch?->name ?? '—' }}</div>
            <div class="sr-time">Opened {{ $s->opened_at->diffForHumans() }}</div>
            <div style="font-size:12px; color:#2ecc71; font-weight:600;">● Open</div>
        </div>
        @endforeach
        @endif
    </div>

    {{-- Recent Orders --}}
    <div class="sv-section">
        <h5>Recent POS Orders (Today)</h5>
        @if($recentOrders->isEmpty())
        <div class="empty-state">No orders yet today.</div>
        @else
        @foreach($recentOrders as $o)
        <div class="order-row">
            <div class="or-id">{{ $o->order_number }}</div>
            <div class="or-cashier">{{ $o->cashier?->name ?? 'N/A' }}</div>
            <div>
                @if($o->posPayment)
                <span class="or-method {{ $o->posPayment->method }}">{{ str_replace('_', ' ', $o->posPayment->method) }}</span>
                @endif
            </div>
            <div style="font-size:11px; color:#aaa;">{{ $o->created_at->format('h:i A') }}</div>
            <div class="or-total">Rs {{ number_format($o->total, 0) }}</div>
        </div>
        @endforeach
        @endif
    </div>
</div>
@endsection
