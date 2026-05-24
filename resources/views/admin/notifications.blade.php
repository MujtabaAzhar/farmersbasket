@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">

        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Notifications</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li><a href="{{ route('admin.index') }}"><div class="text-tiny">Dashboard</div></a></li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Notifications</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="flex items-center justify-between mb-3">
                <div class="fs-14 text-muted">{{ $notifications->total() }} total</div>
                <button id="btn-mark-all-page" class="tf-button style-1" style="padding:6px 16px;font-size:13px;">
                    Mark all as read
                </button>
            </div>

            @forelse($notifications as $n)
            @php
                $icons  = ['new_order'=>'icon-shopping-bag','order_canceled'=>'icon-x-circle','new_contact'=>'icon-mail','new_customer'=>'icon-user','low_stock'=>'icon-alert-triangle'];
                $colors = ['new_order'=>'#2ecc71','order_canceled'=>'#e74c3c','new_contact'=>'#3498db','new_customer'=>'#9b59b6','low_stock'=>'#f39c12'];
                $icon   = $icons[$n->type]  ?? 'icon-bell';
                $color  = $colors[$n->type] ?? '#888';
            @endphp
            <div class="d-flex align-items-start gap-3 py-3 notif-row {{ $n->is_read ? '' : 'unread' }}"
                 style="border-bottom:1px solid #f0f0f0;{{ $n->is_read ? '' : 'background:#f0fdf4;margin:0 -24px;padding-left:24px;padding-right:24px;' }}"
                 data-id="{{ $n->id }}">
                <div style="width:40px;height:40px;border-radius:50%;background:{{ $color }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <i class="{{ $icon }}" style="color:{{ $color }};font-size:18px;"></i>
                </div>
                <div style="flex:1;min-width:0;">
                    <div class="fw-600 fs-14">{{ $n->title }}</div>
                    <div class="fs-13 text-muted mt-1">{{ $n->message }}</div>
                    <div class="fs-11 text-muted mt-1">{{ $n->time_ago }} · {{ $n->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="d-flex align-items-center gap-2 flex-shrink-0">
                    @if($n->url)
                    <a href="{{ $n->url }}" class="tf-button" style="padding:4px 12px;font-size:12px;">View</a>
                    @endif
                    @if(!$n->is_read)
                    <button class="btn-mark-one tf-button" data-id="{{ $n->id }}"
                            style="padding:4px 12px;font-size:12px;background:#f0f0f0;color:#555;">
                        Mark read
                    </button>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-5">
                <i class="icon-bell" style="font-size:40px;display:block;margin-bottom:10px;opacity:.3;"></i>
                No notifications yet.
            </div>
            @endforelse

            <div class="divider mt-3"></div>
            <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                {{ $notifications->links('pagination::bootstrap-5') }}
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.getElementById('btn-mark-all-page').addEventListener('click', function() {
    $.post('{{ route('admin.notifications.read.all') }}', {}, function() {
        document.querySelectorAll('.notif-row.unread').forEach(function(row) {
            row.classList.remove('unread');
            row.style.background = '';
            row.style.paddingLeft = '';
            row.style.paddingRight = '';
            row.style.marginLeft = '';
            row.style.marginRight = '';
            var btn = row.querySelector('.btn-mark-one');
            if (btn) btn.remove();
        });
    });
});

document.querySelectorAll('.btn-mark-one').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id  = this.dataset.id;
        var row = this.closest('.notif-row');
        $.post('{{ url('/admin/notifications') }}/' + id + '/read', {}, function() {
            row.classList.remove('unread');
            row.style.background = '';
            row.style.paddingLeft = '';
            row.style.paddingRight = '';
            row.style.marginLeft = '';
            row.style.marginRight = '';
            btn.remove();
        });
    });
});
</script>
@endpush
@endsection
