@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Stock Transfers</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock Transfers</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-20">{{ session('error') }}</div>
        @endif

        <div class="wg-box">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                <h5>Transfer Log</h5>
                <a href="{{ route('admin.stock.transfer.create') }}" class="tf-button style-1">+ New Transfer</a>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product</th>
                            <th>From</th>
                            <th>To</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Status</th>
                            <th>Note</th>
                            <th>By</th>
                            <th>Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                        <tr>
                            <td>{{ $transfer->id }}</td>
                            <td>
                                <div class="fw-500">{{ $transfer->product->name ?? '—' }}</div>
                                @if($transfer->variant)
                                    <small class="text-muted">{{ $transfer->variant->display_label }}</small>
                                @endif
                            </td>
                            <td>
                                @if($transfer->fromWarehouse)
                                    <span class="badge bg-secondary">{{ $transfer->fromWarehouse->code }}</span>
                                    <small class="d-block">{{ $transfer->fromWarehouse->name }}</small>
                                @else
                                    <span class="badge bg-primary">Main Stock</span>
                                @endif
                            </td>
                            <td>
                                @if($transfer->toWarehouse)
                                    <span class="badge bg-secondary">{{ $transfer->toWarehouse->code }}</span>
                                    <small class="d-block">{{ $transfer->toWarehouse->name }}</small>
                                @else
                                    <span class="badge bg-primary">Main Stock</span>
                                @endif
                            </td>
                            <td class="text-center fw-bold">{{ $transfer->quantity }}</td>
                            <td class="text-center">
                                @if($transfer->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($transfer->status === 'cancelled')
                                    <span class="badge bg-secondary">Cancelled</span>
                                @else
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </td>
                            <td><small>{{ $transfer->note ?: '—' }}</small></td>
                            <td><small>{{ $transfer->creator->name ?? '—' }}</small></td>
                            <td><small>{{ $transfer->created_at->format('d M Y H:i') }}</small></td>
                            <td class="text-center">
                                @if($transfer->status === 'pending')
                                <div class="d-flex gap-1 justify-content-center">
                                    <form action="{{ route('admin.stock.transfer.complete', $transfer->id) }}" method="POST"
                                          onsubmit="return confirm('Complete this transfer? This will move stock immediately.')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-success">Complete</button>
                                    </form>
                                    <form action="{{ route('admin.stock.transfer.cancel', $transfer->id) }}" method="POST"
                                          onsubmit="return confirm('Cancel this transfer?')">
                                        @csrf @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-secondary">Cancel</button>
                                    </form>
                                </div>
                                @else
                                    @if($transfer->completed_at)
                                        <small class="text-muted">{{ $transfer->completed_at->format('d M Y') }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center py-4 text-muted">No transfers yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $transfers->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
</div>
@endsection
