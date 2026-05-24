@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>{{ $warehouse->name }} — Stock</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.warehouses') }}"><div class="text-tiny">Warehouses</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Stock</div></li>
            </ul>
        </div>

        @if(session('status'))
            <div class="alert alert-success mb-20">{{ session('status') }}</div>
        @endif

        {{-- Warehouse info --}}
        <div class="wg-box mb-20">
            <div class="row g-3">
                <div class="col-md-3">
                    <small class="text-muted d-block">Code</small>
                    <strong>{{ $warehouse->code }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">City</small>
                    <strong>{{ $warehouse->city ?: '—' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Manager</small>
                    <strong>{{ $warehouse->manager_name ?: '—' }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-{{ $warehouse->is_active ? 'success' : 'secondary' }}">
                        {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Current stock --}}
        <div class="wg-box mb-20">
            <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                <h5>Current Stock</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.stock.transfer.create') }}" class="tf-button style-1">+ Transfer Stock</a>
                    <a href="{{ route('admin.warehouses') }}" class="tf-button" style="background:#6c757d;">Back</a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>SKU</th>
                            <th>Variant</th>
                            <th class="text-center">Qty</th>
                            <th class="text-center">Alert</th>
                            <th class="text-center">Adjust</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventories as $inv)
                        <tr class="{{ $inv->quantity <= 0 ? 'table-danger' : ($inv->quantity <= 10 ? 'table-warning' : '') }}">
                            <td class="fw-500">{{ $inv->product->name ?? '—' }}</td>
                            <td><span class="badge bg-secondary">{{ $inv->product->sku ?? '—' }}</span></td>
                            <td>{{ $inv->variant?->display_label ?? '—' }}</td>
                            <td class="text-center fw-bold">{{ $inv->quantity }}</td>
                            <td class="text-center">
                                @if($inv->quantity <= 0)
                                    <span class="badge bg-danger">Out</span>
                                @elseif($inv->quantity <= 10)
                                    <span class="badge bg-warning text-dark">Low</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal" data-bs-target="#whAdjustModal"
                                    data-product-id="{{ $inv->product_id }}"
                                    data-variant-id="{{ $inv->variant_id }}"
                                    data-name="{{ $inv->product->name ?? '' }}"
                                    data-qty="{{ $inv->quantity }}">
                                    Adjust
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No stock assigned to this warehouse yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">{{ $inventories->links('pagination::bootstrap-5') }}</div>
        </div>

        {{-- Add new product stock --}}
        <div class="wg-box">
            <h5 class="mb-20">Add / Set Stock for a Variant</h5>
            <form action="{{ route('admin.warehouse.inventory.adjust', $warehouse->id) }}" method="POST">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-500">Product</label>
                        <select name="product_id" class="form-control" id="wh_product_select" required>
                            <option value="">— Select Product —</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-500">Variant</label>
                        <select name="variant_id" class="form-control" id="wh_variant_select">
                            <option value="">— Select Variant —</option>
                            @foreach($products as $p)
                                @foreach($p->variants as $v)
                                    <option value="{{ $v->id }}" data-product="{{ $p->id }}" style="display:none;">
                                        {{ $v->display_label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-500">Adjustment</label>
                        <select name="adjustment_type" class="form-control">
                            <option value="increase">Add (+)</option>
                            <option value="decrease">Remove (−)</option>
                            <option value="set">Set Exact</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fw-500">Quantity</label>
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-500">Note</label>
                        <input type="text" name="note" class="form-control" maxlength="255" placeholder="Reason">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="tf-button style-1 w-100">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Adjust Modal --}}
<div class="modal fade" id="whAdjustModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.warehouse.inventory.adjust', $warehouse->id) }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" id="wh_modal_product_id">
                <input type="hidden" name="variant_id" id="wh_modal_variant_id">
                <div class="modal-header">
                    <h5 class="modal-title">Adjust Warehouse Stock — <span id="wh_modal_name"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Current warehouse stock: <strong id="wh_modal_qty"></strong></p>
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
                        <input type="number" name="quantity" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-500">Note</label>
                        <input type="text" name="note" class="form-control" maxlength="255">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="tf-button style-1">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#whAdjustModal').on('show.bs.modal', function(e){
        var btn = $(e.relatedTarget);
        $('#wh_modal_product_id').val(btn.data('product-id'));
        $('#wh_modal_variant_id').val(btn.data('variant-id') || '');
        $('#wh_modal_name').text(btn.data('name'));
        $('#wh_modal_qty').text(btn.data('qty'));
    });

    // Filter variants by product
    function filterVariants(){
        var productId = $('#wh_product_select').val();
        var $sel = $('#wh_variant_select');
        $sel.find('option[data-product]').hide();
        $sel.val('');
        if(productId){
            $sel.find('option[data-product="'+productId+'"]').show();
        }
    }
    $('#wh_product_select').on('change', filterVariants);
});
</script>
@endpush
