@extends('layouts.pos')

@push('styles')
<style>
    .sessions-wrap { flex: 1; overflow-y: auto; padding: 20px; }
    .sessions-header { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
    .sessions-header h4 { font-size: 16px; font-weight: 700; color: #1a1f2e; margin: 0; }
    .btn-back { background: none; border: 1px solid #ddd; border-radius: 8px; padding: 7px 14px; font-size: 13px; cursor: pointer; color: #555; }

    .session-panel { background: #fff; border-radius: 10px; border: 1px solid #e8e8e8; padding: 18px 20px; margin-bottom: 16px; }
    .session-panel h5 { font-size: 13px; font-weight: 700; color: #1a1f2e; margin-bottom: 12px; text-transform: uppercase; }
    .form-row { display: flex; gap: 12px; }
    .form-group { flex: 1; margin-bottom: 10px; }
    .form-group label { font-size: 12px; font-weight: 600; color: #555; display: block; margin-bottom: 4px; }
    .form-group input, .form-group textarea {
        width: 100%; border: 1px solid #ddd; border-radius: 6px;
        padding: 7px 10px; font-size: 13px; outline: none;
    }
    .form-group input:focus { border-color: #2ecc71; }

    .btn-open-session { background: #2ecc71; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; font-size: 14px; }
    .btn-close-session { background: #e74c3c; color: #fff; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 700; cursor: pointer; font-size: 14px; }

    .session-status-open { background: #f0fdf4; border: 2px solid #2ecc71; border-radius: 10px; padding: 14px 18px; margin-bottom: 16px; }
    .session-status-open .label { color: #2ecc71; font-weight: 700; font-size: 14px; }
    .session-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 10px; font-size: 12px; }
    .session-info-item { background: #fff; border-radius: 6px; padding: 8px 10px; }
    .session-info-item .si-label { color: #888; }
    .session-info-item .si-val { font-weight: 700; color: #1a1f2e; font-size: 14px; }

    .sessions-table { width: 100%; border-collapse: collapse; font-size: 12px; }
    .sessions-table th { background: #f5f5f5; padding: 8px 10px; text-align: left; font-weight: 600; color: #555; }
    .sessions-table td { padding: 8px 10px; border-bottom: 1px solid #f5f5f5; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-open { background: #d4edda; color: #155724; }
    .badge-closed { background: #f5f5f5; color: #888; }
    .variance-pos { color: #2ecc71; font-weight: 600; }
    .variance-neg { color: #e74c3c; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="sessions-wrap">
    <div class="sessions-header">
        <button class="btn-back" onclick="window.location='{{ route('pos.index') }}'">← Back to POS</button>
        <h4>Shift Management</h4>
    </div>

    @if(session('status'))
    <div style="background:#d4edda; color:#155724; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px;">
        {{ session('status') }}
    </div>
    @endif
    @if($errors->any())
    <div style="background:#fde8e8; color:#e74c3c; border-radius:8px; padding:10px 14px; margin-bottom:14px; font-size:13px;">
        @foreach($errors->all() as $e) <div>• {{ $e }}</div> @endforeach
    </div>
    @endif

    @if($currentSession)
    {{-- Active Session --}}
    <div class="session-status-open">
        <div class="label">● Session Active</div>
        <div class="session-info-grid">
            <div class="session-info-item">
                <div class="si-label">Branch</div>
                <div class="si-val">{{ $currentSession->branch?->name ?? 'N/A' }}</div>
            </div>
            <div class="session-info-item">
                <div class="si-label">Opened At</div>
                <div class="si-val">{{ $currentSession->opened_at->format('h:i A') }}</div>
            </div>
            <div class="session-info-item">
                <div class="si-label">Opening Balance</div>
                <div class="si-val">Rs {{ number_format($currentSession->opening_balance, 0) }}</div>
            </div>
            <div class="session-info-item">
                <div class="si-label">Sales Today</div>
                <div class="si-val">Rs {{ number_format($currentSession->totalSales(), 0) }}</div>
            </div>
        </div>
    </div>

    <div class="session-panel">
        <h5>Close Shift</h5>
        <form action="{{ route('pos.session.close') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Counted Cash in Drawer (Rs)</label>
                    <input type="number" name="closing_balance" min="0" step="0.01" placeholder="0.00" required>
                </div>
            </div>
            <div class="form-group">
                <label>Closing Notes (optional)</label>
                <textarea name="notes" rows="2" placeholder="End of shift notes..."></textarea>
            </div>
            <button type="submit" class="btn-close-session">Close Shift</button>
        </form>
    </div>

    @else
    {{-- Open New Session --}}
    <div class="session-panel">
        <h5>Open New Shift</h5>
        <form action="{{ route('pos.session.open') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label>Opening Cash Balance (Rs)</label>
                    <input type="number" name="opening_balance" min="0" step="0.01" placeholder="0.00" required>
                </div>
            </div>
            <button type="submit" class="btn-open-session">Open Shift</button>
        </form>
    </div>
    @endif

    {{-- Session History --}}
    <div class="session-panel">
        <h5>Session History</h5>
        @if($sessions->isEmpty())
        <div style="text-align:center; color:#aaa; padding:20px; font-size:13px;">No sessions yet.</div>
        @else
        <div style="overflow-x:auto;">
            <table class="sessions-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Branch</th>
                        <th>Opened</th>
                        <th>Closed</th>
                        <th>Opening</th>
                        <th>Expected</th>
                        <th>Counted</th>
                        <th>Variance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sessions as $s)
                    @php
                        $variance = ($s->closing_balance !== null && $s->expected_cash !== null)
                            ? $s->closing_balance - $s->expected_cash
                            : null;
                    @endphp
                    <tr>
                        <td>{{ $s->opened_at->format('d M Y') }}</td>
                        <td>{{ $s->branch?->name ?? '—' }}</td>
                        <td>{{ $s->opened_at->format('h:i A') }}</td>
                        <td>{{ $s->closed_at?->format('h:i A') ?? '—' }}</td>
                        <td>Rs {{ number_format($s->opening_balance, 0) }}</td>
                        <td>{{ $s->expected_cash !== null ? 'Rs '.number_format($s->expected_cash, 0) : '—' }}</td>
                        <td>{{ $s->closing_balance !== null ? 'Rs '.number_format($s->closing_balance, 0) : '—' }}</td>
                        <td>
                            @if($variance !== null)
                                <span class="{{ $variance >= 0 ? 'variance-pos' : 'variance-neg' }}">
                                    {{ $variance >= 0 ? '+' : '' }}Rs {{ number_format($variance, 0) }}
                                </span>
                            @else —
                            @endif
                        </td>
                        <td><span class="badge {{ $s->status === 'open' ? 'badge-open' : 'badge-closed' }}">{{ ucfirst($s->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top:12px;">{{ $sessions->links() }}</div>
        @endif
    </div>
</div>
@endsection
