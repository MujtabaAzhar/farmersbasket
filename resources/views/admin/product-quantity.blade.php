@extends('layouts.admin')
@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Manage Variant Quantities</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><a href="{{ route('admin.products') }}"><div class="text-tiny">Products</div></a></li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">Manage Quantities</div></li>
                </ul>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="wg-box">
                <div class="flex items-center justify-between gap10 flex-wrap mb-20">
                    <h5>Variant Stock Quantities</h5>
                    <a href="{{ route('admin.products') }}" class="tf-button" style="background:#6c757d;">Back to Products</a>
                </div>

                @if($products->count() > 0)
                    <form method="POST" action="{{ route('admin.product.quantity.update') }}" id="quantityForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th class="text-center">Price</th>
                                        <th class="text-center" style="width:160px;">Stock Qty</th>
                                        <th class="text-center">Low Alert</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        @forelse($product->variants as $variant)
                                        <tr>
                                            @if($loop->first)
                                            <td rowspan="{{ $product->variants->count() }}" class="align-middle">
                                                <div class="flex gap10">
                                                    @if($product->image)
                                                        <img src="{{ asset('uploads/products/thumbnails/'.$product->image) }}" alt="" style="width:40px;height:40px;border-radius:4px;object-fit:cover;">
                                                    @endif
                                                    <div>
                                                        <p class="text-black fw-500 mb-0">{{ $product->name }}</p>
                                                        <p class="text-muted mb-0" style="font-size:12px;">{{ $product->category?->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            @endif
                                            <td>
                                                <span class="fw-500">{{ $variant->variant_name }}</span>
                                                @if($variant->weight)
                                                    <small class="text-muted">({{ $variant->weight }} {{ $variant->unit }})</small>
                                                @endif
                                            </td>
                                            <td class="text-center">Rs {{ number_format($variant->price, 0) }}</td>
                                            <td class="text-center">
                                                <input type="number" class="form-control form-control-sm text-center"
                                                    name="quantities[{{ $variant->id }}]"
                                                    value="{{ $variant->stock_qty }}"
                                                    min="0">
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $variant->low_stock_alert }}</span>
                                            </td>
                                            <td class="text-center">
                                                @if($variant->stock_qty <= 0)
                                                    <span class="badge bg-danger">Out</span>
                                                @elseif($variant->stock_qty <= $variant->low_stock_alert)
                                                    <span class="badge bg-warning text-dark">Low</span>
                                                @else
                                                    <span class="badge bg-success">OK</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td>
                                                <div class="flex gap10">
                                                    <div>
                                                        <p class="text-black fw-500 mb-0">{{ $product->name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td colspan="5" class="text-muted text-center">No variants configured</td>
                                        </tr>
                                        @endforelse
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between gap20 flex-wrap mt-20">
                            <button type="submit" class="tf-button" style="background:#4CAF50;" id="saveBtn">Save All Changes</button>
                        </div>
                    </form>

                    @if($products->hasPages())
                        <div class="flex gap10 flex-wrap justify-center mt-20">{{ $products->links() }}</div>
                    @endif
                @else
                    <div class="alert alert-info">
                        No products found. <a href="{{ route('admin.product.add') }}">Add your first product</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
$(function(){
    $('#quantityForm').on('submit', function() {
        $('#saveBtn').prop('disabled', true).text('Saving…');
    });
});
</script>
@endpush
