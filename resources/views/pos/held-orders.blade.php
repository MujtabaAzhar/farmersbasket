@extends('layouts.pos')

@push('styles')
<style>
    .held-wrap { flex: 1; overflow-y: auto; padding: 20px; }
    .held-header { display: flex; align-items: center; gap: 12px; margin-bottom: 18px; }
    .held-header h4 { font-size: 16px; font-weight: 700; color: #1a1f2e; margin: 0; }
    .btn-back { background: none; border: 1px solid #ddd; border-radius: 8px; padding: 7px 14px; font-size: 13px; cursor: pointer; color: #555; }

    .held-card {
        background: #fff; border: 1px solid #e8e8e8; border-radius: 10px;
        padding: 14px 16px; margin-bottom: 12px;
        display: flex; align-items: flex-start; gap: 14px;
    }
    .held-card-body { flex: 1; }
    .held-card-body .held-time { font-size: 11px; color: #888; margin-bottom: 4px; }
    .held-card-body .held-items { font-size: 13px; font-weight: 600; color: #1a1f2e; margin-bottom: 4px; }
    .held-card-body .held-note { font-size: 12px; color: #888; font-style: italic; }
    .held-card-body .held-customer { font-size: 12px; color: #555; margin-top: 4px; }
    .held-actions { display: flex; flex-direction: column; gap: 6px; }
    .btn-resume {
        background: #2ecc71; color: #fff; border: none;
        border-radius: 8px; padding: 8px 16px; font-weight: 700; cursor: pointer; font-size: 13px;
        white-space: nowrap;
    }
    .btn-resume:hover { background: #27ae60; }
    .gift-tag {
        display: inline-block; background: #fff3cd; color: #e67e22;
        border-radius: 20px; padding: 1px 8px; font-size: 11px; font-weight: 600;
        margin-left: 6px;
    }
    .empty-state { text-align: center; color: #aaa; padding: 60px 20px; }
    .empty-state i { font-size: 40px; display: block; margin-bottom: 10px; }
</style>
@endpush

@section('content')
<div class="held-wrap">
    <div class="held-header">
        <button class="btn-back" onclick="window.location='{{ route('pos.index') }}'">← Back to POS</button>
        <h4>Held Orders ({{ $held->count() }})</h4>
    </div>

    @if($held->isEmpty())
    <div class="empty-state">
        <i class="icon-pause"></i>
        <div>No held orders</div>
        <small>Orders you hold will appear here</small>
    </div>
    @else
    @foreach($held as $h)
    @php
        $items = collect($h->cart_data);
        $itemTotal = $items->sum(fn($i) => $i['price'] * $i['qty']);
        $customer = $h->customer_data;
        $isGift = !empty($h->gift_data);
    @endphp
    <div class="held-card">
        <div class="held-card-body">
            <div class="held-time">
                Held {{ $h->created_at->diffForHumans() }} — {{ $h->created_at->format('h:i A, d M') }}
                @if($isGift)<span class="gift-tag">Gift</span>@endif
            </div>
            <div class="held-items">
                {{ $items->count() }} item(s) — Rs {{ number_format($itemTotal, 0) }}
            </div>
            <div style="font-size:12px; color:#555; margin-top:4px;">
                @foreach($items as $it)
                    {{ $it['name'] }} ×{{ $it['qty'] }}@if(!$loop->last), @endif
                @endforeach
            </div>
            @if($customer && !empty($customer['name']))
            <div class="held-customer">Customer: {{ $customer['name'] }}@if(!empty($customer['phone'])) — {{ $customer['phone'] }}@endif</div>
            @endif
            @if($h->note)
            <div class="held-note">"{{ $h->note }}"</div>
            @endif
        </div>
        <div class="held-actions">
            <button class="btn-resume" onclick="resumeHeld({{ $h->id }}, this)">Resume</button>
        </div>
    </div>
    @endforeach
    @endif
</div>
@endsection

@push('scripts')
<script>
function resumeHeld(id, btn) {
    btn.disabled = true;
    btn.textContent = '...';
    $.ajax({
        url: '/pos/held/' + id + '/resume',
        method: 'POST',
        success: function (res) {
            if (res.success) {
                // Store restored state in sessionStorage
                if (res.customerData) {
                    sessionStorage.setItem('pos_customer_type', res.customerData.type || 'walkin');
                    sessionStorage.setItem('pos_customer_id', res.customerData.id || '');
                    sessionStorage.setItem('pos_customer_name', res.customerData.name || '');
                    sessionStorage.setItem('pos_customer_phone', res.customerData.phone || '');
                }
                if (res.giftData) {
                    sessionStorage.setItem('pos_is_gift', '1');
                    sessionStorage.setItem('pos_gift_sender_name', res.giftData.sender_name || '');
                    sessionStorage.setItem('pos_gift_sender_phone', res.giftData.sender_phone || '');
                    sessionStorage.setItem('pos_gift_receiver_name', res.giftData.receiver_name || '');
                    sessionStorage.setItem('pos_gift_receiver_phone', res.giftData.receiver_phone || '');
                    sessionStorage.setItem('pos_gift_receiver_address', res.giftData.receiver_address || '');
                    sessionStorage.setItem('pos_gift_receiver_city', res.giftData.receiver_city || '');
                    sessionStorage.setItem('pos_gift_message', res.giftData.gift_message || '');
                }
                window.location = '{{ route("pos.index") }}';
            }
        },
        error: function () {
            btn.disabled = false;
            btn.textContent = 'Resume';
            alert('Failed to resume order.');
        }
    });
}
</script>
@endpush
