@extends('layouts.admin')
@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>New Stock Transfer</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><a href="{{ route('admin.stock.transfers') }}"><div class="text-tiny">Stock Transfers</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">New</div></li>
            </ul>
        </div>

        <div class="wg-box">
            @if($errors->any())
                <div class="alert alert-danger mb-20">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('admin.stock.transfer.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-500">From <small class="text-muted">(blank = Main Stock)</small></label>
                        <select name="from_warehouse_id" class="form-control">
                            <option value="">— Main Stock —</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('from_warehouse_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">To <small class="text-muted">(blank = Main Stock)</small></label>
                        <select name="to_warehouse_id" class="form-control">
                            <option value="">— Main Stock —</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }} ({{ $wh->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('to_warehouse_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Product <span class="text-danger">*</span></label>
                        <select name="product_id" class="form-control" required id="transfer_product">
                            <option value="">— Select Product —</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-500">Variant <small class="text-muted">(optional)</small></label>
                        <select name="variant_id" class="form-control" id="transfer_variant">
                            <option value="">— Select variant —</option>
                            @foreach($products as $p)
                                @foreach($p->variants as $v)
                                    <option value="{{ $v->id }}"
                                        data-product="{{ $p->id }}"
                                        {{ old('variant_id') == $v->id ? 'selected' : '' }}
                                        style="display:none;">
                                        {{ $v->variant_name }}
                                        @if($v->weight) ({{ $v->weight }} {{ $v->unit }})@endif
                                        — {{ $v->stock_qty }} in stock
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('variant_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-500">Quantity <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" min="1" required>
                        @error('quantity')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-500">Note <small class="text-muted">(optional)</small></label>
                        <input type="text" name="note" class="form-control" value="{{ old('note') }}" maxlength="255">
                    </div>
                    <div class="col-12">
                        <div class="alert alert-info">
                            Transfer is created in <strong>Pending</strong> status. Go to the transfer list and click <strong>Complete</strong> to execute the stock movement.
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="tf-button style-1 me-3">Create Transfer</button>
                        <a href="{{ route('admin.stock.transfers') }}" class="tf-button" style="background:#6c757d;">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(function(){
    function filterVariants(){
        var productId = $('#transfer_product').val();
        var $sel = $('#transfer_variant');
        $sel.find('option[data-product]').hide();
        $sel.val('');
        if(productId){
            $sel.find('option[data-product="'+productId+'"]').show();
        }
    }
    $('#transfer_product').on('change', filterVariants);
    filterVariants();
});
</script>
@endpush
