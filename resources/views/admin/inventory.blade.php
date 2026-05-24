@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Inventory Management</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Inventory</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif

        {{-- Stats --}}
        <div class="row g-3 mb-20">
            <div class="col-md-4">
                <div class="wg-box text-center">
                    <div class="body-title text-muted mb-1">Total Variants</div>
                    <h2 class="mb-0">{{ $total_variants }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wg-box text-center" style="border-left:4px solid #f5c518;">
                    <div class="body-title text-muted mb-1">Low Stock</div>
                    <h2 class="mb-0 text-warning">{{ $low_stock_count }}</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="wg-box text-center" style="border-left:4px solid #dc3545;">
                    <div class="body-title text-muted mb-1">Out of Stock</div>
                    <h2 class="mb-0 text-danger">{{ $out_stock_count }}</h2>
                </div>
            </div>
        </div>

        @if($low_stock_count > 0)
        <div class="alert alert-warning mb-20">
            <strong><i class="icon-alert-triangle me-2"></i>Low Stock Alert:</strong>
            {{ $low_stock_count }} variant(s) are running low.
            <a href="{{ route('admin.inventory', ['filter' => 'low']) }}" class="alert-link ms-2">View →</a>
        </div>
        @endif

        {{-- Filter --}}
        <div class="wg-box mb-20">
            <form method="GET" action="{{ route('admin.inventory') }}" class="d-flex gap-3 flex-wrap align-items-end">
                <div class="flex-grow-1">
                    <input type="text" name="search" class="form-control" placeholder="Search by product, SKU, barcode…" value="{{ request('search') }}">
                </div>
                <div>
                    <select name="filter" class="form-control">
                        <option value="">All</option>
                        <option value="low" {{ request('filter') === 'low' ? 'selected' : '' }}>Low Stock</option>
                        <option value="out" {{ request('filter') === 'out' ? 'selected' : '' }}>Out of Stock</option>
                    </select>
                </div>
                <button type="submit" class="tf-button style-1">Filter</button>
                <a href="{{ route('admin.inventory') }}" class="tf-button" style="background:#6c757d;">Reset</a>
            </form>
        </div>

        {{-- Variants table --}}
        <div class="wg-box">
            <h5 class="mb-20">Variant Stock Levels</h5>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-center">SKU</th>
                            <th class="text-center">Price</th>
                            <th class="text-center">Stock Qty</th>
                            <th class="text-center">Alert</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($variants as $variant)
                        @php
                            $isOut  = $variant->stock_qty <= 0;
                            $isLow  = !$isOut && $variant->stock_qty <= $variant->low_stock_alert;
                            $rowCls = $isOut ? 'table-danger' : ($isLow ? 'table-warning' : '');
                        @endphp
                        <tr class="{{ $rowCls }}">
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    @if($variant->product->image)
                                        <img src="{{ asset('uploads/products/thumbnails/'.$variant->product->image) }}" style="width:32px;height:32px;object-fit:cover;border-radius:4px;">
                                    @endif
                                    <span class="fw-500">{{ $variant->product->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                {{ $variant->variant_name }}
                                @if($variant->weight)
                                    <small class="text-muted">({{ $variant->weight }} {{ $variant->unit }})</small>
                                @endif
                            </td>
                            <td class="text-center"><span class="badge bg-secondary">{{ $variant->sku ?? '—' }}</span></td>
                            <td class="text-center">Rs {{ number_format($variant->price, 0) }}</td>
                            <td class="text-center fw-bold fs-6">{{ $variant->stock_qty }}</td>
                            <td class="text-center"><small class="text-muted">≤ {{ $variant->low_stock_alert }}</small></td>
                            <td class="text-center">
                                @if($isOut)
                                    <span class="badge bg-danger">Out</span>
                                @elseif($isLow)
                                    <span class="badge bg-warning text-dark">Low</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#adjustModal"
                                    data-id="{{ $variant->id }}"
                                    data-name="{{ $variant->product->name ?? '' }} — {{ $variant->variant_name }}"
                                    data-qty="{{ $variant->stock_qty }}">
                                    Adjust
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No variants found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $variants->links('pagination::bootstrap-5') }}</div>
        </div>

        {{-- Recent logs --}}
        <div class="wg-box mt-5">
            <h5 class="mb-20">Recent Inventory Activity</h5>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Variant</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Before</th>
                            <th class="text-center">Change</th>
                            <th class="text-center">After</th>
                            <th>Note</th>
                            <th>By</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_logs as $log)
                        @php
                            $typeBadge = ['order'=>'danger','cancel'=>'success','adjustment'=>'info','transfer_in'=>'primary','transfer_out'=>'secondary','restock'=>'success'];
                        @endphp
                        <tr>
                            <td>{{ $log->product->name ?? '—' }}</td>
                            <td><small>{{ $log->variant->variant_name ?? '—' }}</small></td>
                            <td class="text-center"><span class="badge bg-{{ $typeBadge[$log->type] ?? 'secondary' }}">{{ ucfirst(str_replace('_',' ',$log->type)) }}</span></td>
                            <td class="text-center">{{ $log->quantity_before }}</td>
                            <td class="text-center {{ $log->quantity_change >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ $log->quantity_change >= 0 ? '+' : '' }}{{ $log->quantity_change }}
                            </td>
                            <td class="text-center">{{ $log->quantity_after }}</td>
                            <td><small>{{ $log->note }}</small></td>
                            <td><small>{{ $log->creator->name ?? '—' }}</small></td>
                            <td><small>{{ $log->created_at->format('d M Y H:i') }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-3 text-muted">No inventory activity yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Adjust Modal --}}
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.adjust') }}" method="POST">
                @csrf
                <input type="hidden" name="variant_id" id="modal_variant_id">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Stock — <span id="modal_variant_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Current stock: <strong id="modal_current_qty"></strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-500">Adjustment Type</label>
                        <select name="adjustment_type" class="form-control" required>
                            <option value="increase">Add Stock (+)</option>
                            <option value="decrease">Remove Stock (−)</option>
                            <option value="set">Set Exact Amount</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="0" required placeholder="Enter quantity">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Note <small class="text-muted">(optional)</small></label>
                        <input type="text" name="note" class="form-control" maxlength="255" placeholder="Reason for adjustment">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tf-button style-1">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#adjustModal').on('show.bs.modal', function(e){
        var btn = $(e.relatedTarget);
        $('#modal_variant_id').val(btn.data('id'));
        $('#modal_variant_name').text(btn.data('name'));
        $('#modal_current_qty').text(btn.data('qty'));
    });
});
</script>
@endpush
