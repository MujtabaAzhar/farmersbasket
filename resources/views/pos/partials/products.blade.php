@forelse($products as $product)
@php
    $minPrice = $product->variants->min('price');
    $totalStock = $product->variants->sum('stock_qty');
    $variantCount = $product->variants->count();
@endphp
<div class="product-card {{ $totalStock <= 0 ? 'out-of-stock' : '' }}"
     data-id="{{ $product->id }}"
     data-name="{{ $product->name }}"
     onclick="posOpenVariantModal({{ $product->id }}, '{{ addslashes($product->name) }}')">
    <img src="{{ asset('uploads/products/thumbnails/' . $product->image) }}" alt="{{ $product->name }}"
         onerror="this.src='{{ asset('images/logo/logo.png') }}'">
    <div class="p-name">{{ $product->name }}</div>
    <div class="p-price">
        @if($minPrice)
            Rs {{ number_format($minPrice, 0) }}{{ $variantCount > 1 ? '+' : '' }}
        @else
            —
        @endif
    </div>
    <div class="p-stock">
        @if($totalStock <= 0)
            Out of Stock
        @elseif($totalStock <= 10)
            Low: {{ $totalStock }} left
        @else
            {{ $totalStock }} in stock
        @endif
    </div>
    @if($variantCount > 1)
        <div style="font-size:10px;color:#888;text-align:center;">{{ $variantCount }} variants</div>
    @endif
</div>
@empty
<div style="grid-column:1/-1; text-align:center; padding:40px; color:#aaa;">
    <i class="icon-search" style="font-size:32px; display:block; margin-bottom:8px;"></i>
    No products found. Try a different search.
</div>
@endforelse
