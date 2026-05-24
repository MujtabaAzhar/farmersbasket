@if($items->isEmpty())
<div class="cart-empty">
    <i class="icon-shopping-cart"></i>
    <div>Cart is empty</div>
    <small>Add products from the left panel</small>
</div>
@else
@foreach($items as $item)
<div class="cart-row" id="cart-row-{{ $item->rowId }}">
    <div class="cart-name">{{ $item->name }}</div>
    <div class="cart-qty">
        <button onclick="posUpdateCart('{{ $item->rowId }}', {{ $item->qty - 1 }})">−</button>
        <input type="number" value="{{ $item->qty }}" min="0"
               onchange="posUpdateCart('{{ $item->rowId }}', this.value)"
               style="width:40px; text-align:center;">
        <button onclick="posUpdateCart('{{ $item->rowId }}', {{ $item->qty + 1 }})">+</button>
    </div>
    <div class="cart-price">Rs {{ number_format($item->price * $item->qty, 0) }}</div>
    <button class="cart-del" onclick="posRemoveFromCart('{{ $item->rowId }}')" title="Remove">✕</button>
</div>
@endforeach
@endif
<input type="hidden" id="cart-subtotal" value="{{ $subtotal ?? '0.00' }}">
<input type="hidden" id="cart-tax" value="{{ $tax ?? '0.00' }}">
<input type="hidden" id="cart-total" value="{{ $total ?? '0.00' }}">
