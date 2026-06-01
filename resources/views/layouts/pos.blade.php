<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS — {{ config('app.name', "Farmer's Basket") }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('icon/style.css') }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; overflow: hidden; font-family: 'Segoe UI', sans-serif; background: #f0f2f5; }

        /* ── Top Bar ─────────────────────────────────────────── */
        .pos-topbar {
            height: 52px; background: #198754; color: #fff;
            display: flex; align-items: center; padding: 0 16px; gap: 16px;
            flex-shrink: 0; border-bottom: 2px solid #2ecc71;
        }
        .pos-topbar .brand { font-weight: 700; font-size: 16px; color: #2ecc71; white-space: nowrap; }
        .pos-topbar .info-pill {
            background: rgba(255,255,255,.1); border-radius: 20px;
            padding: 3px 12px; font-size: 12px; white-space: nowrap;
        }
        .pos-topbar .spacer { flex: 1; }
        .pos-topbar .clock { font-size: 14px; font-weight: 600; letter-spacing: 1px; }
        .pos-topbar a { color: #ccc; text-decoration: none; font-size: 12px; }
        .pos-topbar a:hover { color: #fff; }

        /* ── Main Layout ──────────────────────────────────────── */
        .pos-wrapper {
            display: flex; flex-direction: column;
            height: 100vh;
        }
        .pos-main {
            display: flex; flex: 1; overflow: hidden;
        }

        /* ── Left: Products Panel ─────────────────────────────── */
        .pos-left {
            flex: 0 0 62%; display: flex; flex-direction: column;
            border-right: 1px solid #ddd; background: #fff;
        }
        .pos-search-bar {
            padding: 10px 12px; border-bottom: 1px solid #eee;
            display: flex; gap: 8px; align-items: center;
        }
        .pos-search-bar input {
            flex: 1; border: 2px solid #2ecc71; border-radius: 8px;
            padding: 8px 14px; font-size: 15px; outline: none;
        }
        .pos-search-bar input:focus { box-shadow: 0 0 0 3px rgba(46,204,113,.2); }
        .pos-categories {
            display: flex; gap: 6px; padding: 8px 12px;
            overflow-x: auto; flex-shrink: 0; border-bottom: 1px solid #eee;
        }
        .pos-categories::-webkit-scrollbar { height: 4px; }
        .cat-btn {
            background: #f0f2f5; border: none; border-radius: 20px;
            padding: 4px 14px; font-size: 12px; cursor: pointer; white-space: nowrap;
        }
        .cat-btn.active  { background: #2ecc71; color: #fff; }
        .brand-btn {
            background: #f0f2f5; border: none; border-radius: 20px;
            padding: 4px 14px; font-size: 12px; cursor: pointer; white-space: nowrap;
        }
        .brand-btn.active { background: #e67e22; color: #fff; }
        .pos-products-grid {
            flex: 1; overflow-y: auto; padding: 10px 12px;
            display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px;
        }
        .product-card {
            background: #fff; border: 1px solid #e8e8e8; border-radius: 10px;
            padding: 10px; cursor: pointer; transition: all .15s;
            display: flex; flex-direction: column; align-items: center; text-align: center;
        }
        .product-card:hover { border-color: #2ecc71; box-shadow: 0 2px 10px rgba(46,204,113,.2); transform: translateY(-1px); }
        .product-card img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; margin-bottom: 6px; }
        .product-card .p-name { font-size: 12px; font-weight: 600; line-height: 1.3; color: #1a1f2e; margin-bottom: 3px; }
        .product-card .p-price { font-size: 13px; font-weight: 700; color: #2ecc71; }
        .product-card .p-stock { font-size: 10px; color: #888; }
        .product-card.out-of-stock { opacity: .5; cursor: not-allowed; }

        /* ── Right: Order Panel ───────────────────────────────── */
        .pos-right {
            flex: 0 0 38%; display: flex; flex-direction: column;
            background: #fff; overflow: scroll;
        }

        /* Customer Section */
        .pos-customer {
            padding: 8px 12px; border-bottom: 1px solid #eee; flex-shrink: 0; max-height: 320px; overflow-y: auto;
        }
        #cust-phone:focus { border-color: #2ecc71 !important; box-shadow: 0 0 0 3px rgba(46,204,113,.15); }
        .cust-chip {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 6px;
        }
        .cust-chip-found { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .cust-chip-new   { background: #fff3e0; color: #e65100; border: 1px solid #ffcc80; }
        .ot-toggle { display: flex; gap: 4px; margin: 4px 0 6px; }
        .ot-btn {
            flex: 1; padding: 5px 4px; border: 2px solid #ddd; border-radius: 8px;
            background: #f5f5f5; font-size: 12px; font-weight: 600; cursor: pointer;
        }
        .ot-btn.active   { border-color: #2ecc71; background: #f0fdf4; color: #1a6b3a; }
        .ot-btn.gift-on  { border-color: #9b59b6; background: #f9f0ff; color: #6c3483; }
        .cust-section-label { font-size: 10px; font-weight: 700; text-transform: uppercase; color: #888; margin: 4px 0 3px; }
        .addr-item {
            display: flex; align-items: center; gap: 5px; padding: 5px 8px;
            border: 2px solid #eee; border-radius: 8px; margin-bottom: 3px;
            cursor: pointer; font-size: 12px; transition: border-color .1s;
        }
        .addr-item:hover, .addr-item.selected { border-color: #2ecc71; background: #f0fdf4; }
        .addr-item-title { font-weight: 700; flex-shrink: 0; color: #1a1f2e; min-width: 60px; }
        .addr-item-text  { flex: 1; color: #555; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 11px; }
        .addr-item-edit  { background: none; border: none; color: #bbb; font-size: 11px; cursor: pointer; padding: 0 2px; flex-shrink: 0; }
        .addr-item-edit:hover { color: #2ecc71; }
        .btn-add-addr {
            width: 100%; padding: 4px; border: 1px dashed #2ecc71; border-radius: 6px;
            background: none; color: #2ecc71; font-size: 11px; font-weight: 600; cursor: pointer; margin-top: 2px;
        }
        .btn-add-addr:hover { background: #f0fdf4; }
        .addr-form-wrap { background: #f8fffe; border: 1px solid #c8e6c9; border-radius: 8px; padding: 8px; margin-top: 4px; }
        .cust-mini-input {
            width: 100%; border: 1px solid #ddd; border-radius: 6px;
            padding: 5px 8px; font-size: 12px; margin-bottom: 4px; outline: none;
        }
        .cust-mini-input:focus { border-color: #2ecc71; }
        .save-cust-label { display: flex; align-items: center; gap: 5px; font-size: 11px; color: #666; margin-top: 4px; cursor: pointer; }
        .save-cust-label input { width: 13px; height: 13px; margin: 0; }
        /* Legacy — kept for backward compatibility */
        .customer-result-item { padding: 6px 8px; cursor: pointer; border-radius: 6px; font-size: 12px; }
        .customer-result-item:hover { background: #f0f9f0; }

        /* Gift Toggle */
        .gift-toggle-bar {
            padding: 8px 12px; border-bottom: 1px solid #eee;
            display: flex; align-items: center; gap: 10px; flex-shrink: 0;
        }
        .gift-toggle-bar label { font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 6px; }
        .gift-toggle-bar input[type=checkbox] { width: 16px; height: 16px; }

        /* Cart */
        .pos-cart-items {
            flex: 1; overflow-y: auto; padding: 8px 12px;
        }
        .cart-row {
            display: flex; align-items: center; gap-: 6px; padding: 7px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .cart-row .cart-name { flex: 1; font-size: 12px; font-weight: 500; }
        .cart-qty { display: flex; align-items: center; gap: 4px; }
        .cart-qty button {
            width: 24px; height: 24px; border: 1px solid #ddd; border-radius: 4px;
            background: #f5f5f5; font-size: 14px; cursor: pointer; display: flex; align-items: center; justify-content: center;
        }
        .cart-qty input { width: 40px; text-align: center; border: 1px solid #ddd; border-radius: 4px; padding: 2px 4px; font-size: 12px; }
        .cart-price { font-size: 12px; font-weight: 600; color: #1a1f2e; min-width: 60px; text-align: right; }
        .cart-del { color: #e74c3c; cursor: pointer; padding: 0 4px; background: none; border: none; font-size: 14px; }

        /* Cart Summary */
        .pos-cart-summary {
            border-top: 2px solid #eee; padding: 10px 12px; flex-shrink: 0;
            background: #fafafa;
        }
        .summary-row { display: flex; justify-content: space-between; font-size: 12px; margin-bottom: 4px; color: #555; }
        .summary-row.total { font-size: 16px; font-weight: 700; color: #1a1f2e; margin-top: 6px; border-top: 1px solid #ddd; padding-top: 6px; }

        /* Coupon picker */
        .coupon-row { margin: 0 0 6px; }
        .coupon-row select {
            width: 100%; border: 1px solid #ddd; border-radius: 6px;
            padding: 5px 8px; font-size: 12px; outline: none; background: #fff; cursor: pointer;
        }
        .coupon-row select:focus { border-color: #2ecc71; box-shadow: 0 0 0 2px rgba(46,204,113,.15); }
        #coupon-feedback { font-size: 11px; min-height: 16px; margin-top: 3px; }

        /* Action buttons */
        .pos-actions {
            padding: 10px 12px; display: flex; gap: 8px; flex-shrink: 0; border-top: 1px solid #eee;
        }
        .btn-hold {
            flex: 0 0 auto; background: #f39c12; color: #fff; border: none;
            border-radius: 8px; padding: 10px 16px; font-weight: 600; cursor: pointer; font-size: 13px;
        }
        .btn-checkout {
            flex: 1; background: #2ecc71; color: #fff; border: none;
            border-radius: 8px; padding: 10px; font-size: 16px; font-weight: 700; cursor: pointer;
        }
        .btn-hold:hover { background: #e67e22; }
        .btn-checkout:hover { background: #27ae60; }
        .btn-checkout:disabled { background: #95a5a6; cursor: not-allowed; }

        /* Held orders badge */
        .held-badge {
            background: #e74c3c; color: #fff; border-radius: 50%; width: 18px; height: 18px;
            font-size: 10px; display: inline-flex; align-items: center; justify-content: center;
            margin-left: 4px;
        }

        /* Empty cart placeholder */
        .cart-empty {
            text-align: center; color: #aaa; padding: 40px 20px;
        }
        .cart-empty i { font-size: 40px; margin-bottom: 10px; display: block; }

        /* Gift form panel */
        #gift-panel {
            display: none; padding: 0 12px 8px; border-bottom: 1px solid #eee;
            background: #fff9f0; flex-shrink: 0; max-height: 400px; overflow-y: auto;
        }
        #gift-panel h6 { font-size: 11px; font-weight: 700; color: #e67e22; text-transform: uppercase; margin: 8px 0 4px; }
        #gift-panel input, #gift-panel select, #gift-panel textarea {
            width: 100%; border: 1px solid #f0c890; border-radius: 6px;
            padding: 5px 8px; font-size: 12px; margin-bottom: 4px; background: #fff;
        }
        #gift-panel .gift-row { display: flex; gap: 6px; }
        #gift-panel .gift-row input { flex: 1; }


        @media print {
            .pos-topbar, .pos-left, .pos-actions, .pos-cart-summary, .pos-customer, .gift-toggle-bar { display: none !important; }
        }

        /* ── POS Loading Overlay ──────────────────────────────── */
        #pos-loader {
            position: fixed; inset: 0; z-index: 99999;
            background: rgba(15, 20, 35, 0.72);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; pointer-events: none;
            transition: opacity .22s ease;
        }
        #pos-loader.show { opacity: 1; pointer-events: all; }

        .pos-loader-card {
            background: #fff; border-radius: 18px;
            padding: 36px 44px 32px; text-align: center;
            box-shadow: 0 24px 64px rgba(0,0,0,.35);
            min-width: 230px; max-width: 90vw;
            transform: translateY(0);
        }

        .pos-loader-ring {
            width: 56px; height: 56px; margin: 0 auto 18px; position: relative;
        }
        .pos-loader-ring svg {
            width: 56px; height: 56px;
            animation: pos-spin .85s linear infinite;
        }
        @keyframes pos-spin { to { transform: rotate(360deg); } }

        .pos-loader-title {
            font-size: 15px; font-weight: 700; color: #1a1f2e; margin-bottom: 6px; letter-spacing: .2px;
        }
        .pos-loader-sub {
            font-size: 12px; color: #888; min-height: 16px;
        }
        .pos-loader-dots::after {
            content: '';
            animation: pos-dots 1.4s steps(4, end) infinite;
        }
        @keyframes pos-dots {
            0%   { content: ''; }
            25%  { content: '.'; }
            50%  { content: '..'; }
            75%  { content: '...'; }
            100% { content: ''; }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- ── Global POS Loading Overlay ── --}}
<div id="pos-loader">
    <div class="pos-loader-card">
        <div class="pos-loader-ring">
            <svg viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="28" cy="28" r="23" stroke="#e8f5e9" stroke-width="5"/>
                <path d="M28 5 A23 23 0 0 1 51 28" stroke="#2ecc71" stroke-width="5" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="pos-loader-title" id="pos-loader-title">Please wait</div>
        <div class="pos-loader-sub pos-loader-dots" id="pos-loader-sub"></div>
    </div>
</div>

<div class="pos-wrapper">
    {{-- Top Bar --}}
    <div class="pos-topbar">
        <div class="brand"> <img src="{{ asset('images/logo/logo.png') }}" alt="Farmer's Basket" height="40"> POS</div>
        @if(isset($branch) && $branch)
            <div class="info-pill">📍 {{ $branch->name }}</div>
        @endif
        @if(isset($user))
            <div class="info-pill">👤 {{ $user->name }} <small>({{ $user->roleBadge() }})</small></div>
        @endif
        @if(isset($session) && $session)
            <div class="info-pill" style="color:#2ecc71;">● Session Open</div>
        @else
            <div class="info-pill" style="color:#e74c3c;">● No Session</div>
        @endif
        <div class="spacer"></div>
        <div class="clock" id="pos-clock"></div>
        @if(isset($user) && $user->isSupervisor())
            <a href="{{ route('pos.supervisor') }}">Dashboard</a>
        @endif
        <a href="{{ route('pos.sessions') }}">Shifts</a>
        <form action="{{ route('logout') }}" method="POST" style="display:inline">
            @csrf
            <button type="submit" style="background:none;border:none;color:#ccc;cursor:pointer;font-size:12px;">Logout</button>
        </form>
    </div>

    @yield('content')
</div>

<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script>
    // Live clock
    function updateClock(){
        var d = new Date();
        document.getElementById('pos-clock').textContent =
            d.toLocaleTimeString('en-PK', {hour:'2-digit', minute:'2-digit', second:'2-digit'});
    }
    updateClock();
    setInterval(updateClock, 1000);

    // CSRF header for all AJAX
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
</script>

<script>
// ─── POS Loader ───────────────────────────────────────────────────────────────
var PosLoader = (function () {
    var el, titleEl, subEl, _timer;

    function init() {
        el      = document.getElementById('pos-loader');
        titleEl = document.getElementById('pos-loader-title');
        subEl   = document.getElementById('pos-loader-sub');
    }

    function show(title, sub) {
        clearTimeout(_timer);
        if (titleEl) titleEl.textContent = title || 'Please wait';
        if (subEl)   subEl.textContent   = sub   || '';
        if (el)      el.classList.add('show');
    }

    function hide(delay) {
        _timer = setTimeout(function () {
            if (el) el.classList.remove('show');
        }, delay || 0);
    }

    // Show on any page navigation (covers CHECKOUT → redirect, logout, etc.)
    window.addEventListener('beforeunload', function () {
        show('Loading', '');
    });

    // Hide if browser restores the page from back/forward cache (bfcache)
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { hide(0); }
    });

    document.addEventListener('DOMContentLoaded', init);

    return { show: show, hide: hide };
}());
</script>
@stack('scripts')
</body>
</html>
