@extends('layouts.admin')

@section('content')
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Manage Product Quantities</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.products') }}">
                            <div class="text-tiny">Products</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Manage Quantities</div>
                    </li>
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
                    <h5>Product Quantities</h5>
                    <a href="{{ route('admin.products') }}" class="tf-button" style="background-color: #6c757d;">
                        <i class="fas fa-arrow-left"></i> Back to Products
                    </a>
                </div>

                @if($products->count() > 0)
                    <form method="POST" action="{{ route('admin.product.quantity.update') }}" id="quantityForm">
                        @csrf
                        
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-center">Product</th>
                                        <th class="text-center">SKU</th>
                                        <th class="text-center">Size Variants</th>
                                        <th class="text-center">Total Stock</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($products as $product)
                                        <tr>
                                            <td>
                                                <div class="flex gap10">
                                                    @if($product->image)
                                                        <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" alt="{{ $product->name }}" style="width: 40px; height: 40px; border-radius: 4px; object-fit: cover;">
                                                    @else
                                                        <div style="width: 40px; height: 40px; background-color: #f0f0f0; border-radius: 4px;"></div>
                                                    @endif
                                                    <div>
                                                        <p class="text-black fw-500 mb-0">{{ $product->name }}</p>
                                                        <p class="text-muted mb-0" style="font-size: 12px;">{{ $product->category?->name ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $product->SKU }}</span>
                                            </td>
                                            <td>
                                                @if($product->sizes->count() > 0)
                                                    <div class="size-table mb-3">
                                                        <table class="table table-sm" style="margin-bottom: 0;">
                                                            <tbody>
                                                                @foreach($product->sizes as $size)
                                                                    <tr>
                                                                        <td style="width: 40%; padding: 8px 0;">
                                                                            <small><strong>{{ $size->size_label }}</strong></small>
                                                                        </td>
                                                                        <td style="width: 60%; padding: 8px 0;">
                                                                            <input type="number" class="form-control form-control-sm size-quantity" 
                                                                                name="quantities[{{ $size->id }}]" 
                                                                                value="{{ $size->quantity }}" 
                                                                                min="0" 
                                                                                placeholder="Qty">
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @else
                                                    <span class="text-muted">No sizes configured</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="total-stock-{{ $product->id }}" style="font-weight: bold; font-size: 16px;">
                                                    {{ $product->sizes->sum('quantity') }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($product->stock_status == 'instock')
                                                    <span class="badge bg-success">In Stock</span>
                                                @else
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="flex items-center justify-between gap20 flex-wrap mt-20">
                            <button type="submit" class="tf-button" style="background-color: #4CAF50;">
                                <i class="fas fa-save"></i> Save All Changes
                            </button>
                        </div>
                    </form>

                    <!-- Pagination -->
                    @if($products->hasPages())
                        <div class="flex gap10 flex-wrap justify-center mt-20">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle"></i> No products found. <a href="{{ route('admin.product.add') }}">Add your first product</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function(){
            // Update total stock when quantity changes
            $('.size-quantity').on('change', function() {
                // Get the product row and recalculate total
                const row = $(this).closest('tr');
                let total = 0;
                
                row.find('.size-quantity').each(function() {
                    const val = parseInt($(this).val()) || 0;
                    total += val;
                });
                
                // This would need to be done per product - not per row
            });

            // Form submission
            $('#quantityForm').on('submit', function() {
                // Optional: Add loading state
                $(this).find('button[type="submit"]').prop('disabled', true).text('Saving...');
            });
        });
    </script>
@endpush
